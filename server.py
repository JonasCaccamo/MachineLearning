from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
import joblib
import logging
import os
from datetime import datetime
from sklearn.neural_network import MLPClassifier
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.model_selection import train_test_split
from sklearn.metrics import classification_report, roc_auc_score, confusion_matrix, roc_curve
import matplotlib.pyplot as plt
import seaborn as sns

# Logging
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s'
)

# Inizializza Flask
app = Flask(__name__)
CORS(app)

class CyberSecurityMLP:
    def __init__(self, hidden_layer_sizes=(100, 50), activation='relu', 
                 solver='adam', alpha=0.0001, learning_rate_init=0.001,
                 max_iter=500, random_state=42):
        """
        Inizializza il classificatore MLP per cybersecurity
        
        Parameters:
        - hidden_layer_sizes: tuple, dimensioni dei layer nascosti
        - activation: str, funzione di attivazione ('relu', 'tanh', 'logistic')
        - solver: str, algoritmo di ottimizzazione ('adam', 'sgd', 'lbfgs')
        - alpha: float, parametro di regolarizzazione L2
        - learning_rate_init: float, learning rate iniziale
        - max_iter: int, numero massimo di iterazioni
        - random_state: int, seed per riproducibilità
        """
        self.mlp = MLPClassifier(
            hidden_layer_sizes=hidden_layer_sizes,
            activation=activation,
            solver=solver,
            alpha=alpha,
            learning_rate_init=learning_rate_init,
            max_iter=max_iter,
            random_state=random_state
        )
        self.scaler = StandardScaler()
        self.label_encoder = LabelEncoder()
        self.feature_names = None
        
    def preprocess_data(self, X, y=None, fit_transform=True):
        """
        Preprocessa i dati: scaling delle features e encoding delle label
        
        Parameters:
        - X: DataFrame o array, features
        - y: Series o array, target (opzionale per predict)
        - fit_transform: bool, se True fit e transform, altrimenti solo transform
        
        Returns:
        - X_scaled: array, features scalate
        - y_encoded: array, target encodate (se y è fornito)
        """
        if isinstance(X, pd.DataFrame):
            self.feature_names = X.columns.tolist()
            
        if fit_transform:
            X_scaled = self.scaler.fit_transform(X)
            if y is not None:
                y_encoded = self.label_encoder.fit_transform(y)
                return X_scaled, y_encoded
        else:
            X_scaled = self.scaler.transform(X)
            if y is not None:
                y_encoded = self.label_encoder.transform(y)
                return X_scaled, y_encoded
                
        return X_scaled
    
    def train(self, X_train, y_train, validation_split=0.2):
        """
        Addestra il modello MLP
        
        Parameters:
        - X_train: DataFrame o array, features di training
        - y_train: Series o array, target di training
        - validation_split: float, percentuale per validation set
        """
        # Preprocessing
        X_scaled, y_encoded = self.preprocess_data(X_train, y_train, fit_transform=True)
        
        # Split per validation
        if validation_split > 0:
            X_tr, X_val, y_tr, y_val = train_test_split(
                X_scaled, y_encoded, test_size=validation_split, 
                random_state=42, stratify=y_encoded
            )
            
            # Training
            self.mlp.fit(X_tr, y_tr)
            
            # Valutazione su validation set
            val_score = self.mlp.score(X_val, y_val)
            print(f"Validation Accuracy: {val_score:.4f}")
            
            # Predizioni per validation
            y_val_pred = self.mlp.predict(X_val)
            y_val_proba = self.mlp.predict_proba(X_val)[:, 1]
            
            print("\nValidation Classification Report:")
            print(classification_report(y_val, y_val_pred, 
                                      target_names=['Honest', 'Attacker']))
            
            auc_score = roc_auc_score(y_val, y_val_proba)
            print(f"Validation AUC Score: {auc_score:.4f}")
            
        else:
            # Training su tutto il dataset
            self.mlp.fit(X_scaled, y_encoded)
            
        print(f"Training completed. Final loss: {self.mlp.loss_:.4f}")
    
    def predict(self, X_test):
        """
        Effettua predizioni sui dati di test
        
        Parameters:
        - X_test: DataFrame o array, features di test
        
        Returns:
        - predictions: array, predizioni binarie
        - probabilities: array, probabilità di essere attaccante
        """
        X_scaled = self.preprocess_data(X_test, fit_transform=False)
        predictions = self.mlp.predict(X_scaled)
        probabilities = self.mlp.predict_proba(X_scaled)[:, 1]
        
        # Decodifica le predizioni
        predictions_decoded = self.label_encoder.inverse_transform(predictions)
        
        return predictions_decoded, probabilities
    
    def evaluate(self, X_test, y_test):
        """
        Valuta le performance del modello su test set
        
        Parameters:
        - X_test: DataFrame o array, features di test
        - y_test: Series o array, target di test
        """
        predictions, probabilities = self.predict(X_test)
        
        # Encoding del target per le metriche
        y_test_encoded = self.label_encoder.transform(y_test)
        predictions_encoded = self.label_encoder.transform(predictions)
        
        # Metriche
        accuracy = self.mlp.score(self.scaler.transform(X_test), y_test_encoded)
        auc_score = roc_auc_score(y_test_encoded, probabilities)
        
        print("=== TEST SET EVALUATION ===")
        print(f"Accuracy: {accuracy:.4f}")
        print(f"AUC Score: {auc_score:.4f}")
        print(f"Number of test samples: {len(y_test)}")
        
        print("\nClassification Report:")
        print(classification_report(y_test, predictions, 
                                  target_names=['Honest', 'Attacker']))
        
        # Confusion Matrix
        cm = confusion_matrix(y_test_encoded, predictions_encoded)
        plt.figure(figsize=(8, 6))
        sns.heatmap(cm, annot=True, fmt='d', cmap='Blues', 
                   xticklabels=['Honest', 'Attacker'], 
                   yticklabels=['Honest', 'Attacker'])
        plt.title('Confusion Matrix')
        plt.xlabel('Predicted')
        plt.ylabel('Actual')
        plt.show()
        
        # ROC Curve
        fpr, tpr, _ = roc_curve(y_test_encoded, probabilities)
        plt.figure(figsize=(8, 6))
        plt.plot(fpr, tpr, color='blue', lw=2, 
                label=f'ROC Curve (AUC = {auc_score:.3f})')
        plt.plot([0, 1], [0, 1], color='gray', lw=2, linestyle='--')
        plt.xlim([0.0, 1.0])
        plt.ylim([0.0, 1.05])
        plt.xlabel('False Positive Rate')
        plt.ylabel('True Positive Rate')
        plt.title('ROC Curve')
        plt.legend(loc="lower right")
        plt.grid(True)
        plt.show()
        
        return accuracy, auc_score
    
    def plot_training_curve(self):
        """
        Visualizza la curva di loss durante il training
        """
        if hasattr(self.mlp, 'loss_curve_'):
            plt.figure(figsize=(10, 6))
            plt.plot(self.mlp.loss_curve_, 'b-', linewidth=2)
            plt.title('Training Loss Curve')
            plt.xlabel('Iterations')
            plt.ylabel('Loss')
            plt.grid(True)
            plt.show()
        else:
            print("Loss curve not available. Set validation_fraction > 0 during training.")
    
    def get_feature_importance(self, X_sample):
        """
        Calcola l'importance approssimata delle features usando permutation importance
        
        Parameters:
        - X_sample: DataFrame, campione rappresentativo del dataset
        
        Returns:
        - feature_importance: dict, importance per feature
        """
        if self.feature_names is None:
            print("Feature names not available")
            return None
            
        X_scaled = self.scaler.transform(X_sample)
        baseline_pred = self.mlp.predict_proba(X_scaled)[:, 1].mean()
        
        importance_scores = {}
        
        for i, feature_name in enumerate(self.feature_names):
            # Permuta la feature
            X_permuted = X_scaled.copy()
            np.random.shuffle(X_permuted[:, i])
            
            # Calcola la predizione con feature permutata
            permuted_pred = self.mlp.predict_proba(X_permuted)[:, 1].mean()
            
            # Importance = differenza nelle predizioni
            importance_scores[feature_name] = abs(baseline_pred - permuted_pred)
        
        # Ordina per importance
        sorted_importance = dict(sorted(importance_scores.items(), 
                                      key=lambda x: x[1], reverse=True))
        
        return sorted_importance
    
# Carica il modello
MODEL_PATH = 'cybersecurity_mlp_model.joblib'
model = None

def load_model():
    """Carica il modello ML"""
    global model
    
    # Controlla prima nella directory corrente
    if os.path.exists(MODEL_PATH):
        try:
            model = joblib.load(MODEL_PATH)
            logging.info(f"✓ Modello caricato con successo da: {MODEL_PATH}")
            return True
        except Exception as e:
            logging.error(f"✗ Errore nel caricamento del modello: {e}")
            return False
    else:
        # Mostra il percorso corrente per debug
        current_dir = os.getcwd()
        logging.error(f"✗ File modello non trovato in: {os.path.join(current_dir, MODEL_PATH)}")
        logging.info(f"Directory corrente: {current_dir}")
        logging.info(f"File nella directory: {os.listdir(current_dir)}")
        return False

# Carica il modello all'avvio
model_loaded = load_model()

# Endpoint per predizione
@app.route('/api/detect-attack', methods=['POST'])
def detect_attack():
    global model
    
    try:
        # Controlla se il modello è caricato
        if model is None:
            logging.error("Modello non disponibile!")
            return jsonify({
                "error": "Modello non caricato",
                "message": "Il modello ML non è disponibile. Controlla che il file 'cybersecurity_mlp_model.joblib' sia nella directory corretta."
            }), 500
        
        # Ricevi i dati
        data = request.json
        username = data.get('username', data.get('user_id', 'sconosciuto'))
        logging.info(f"=== Nuova richiesta di analisi per utente: {username} ===")
        
        # Log dei dati ricevuti per debug
        logging.debug(f"Dati ricevuti: {data}")
        
        # Prepara le features dal frontend
        # Il frontend invia questi campi, dobbiamo mapparli alle features del modello
        feature_mapping = {
            'network_packet_size': 400,  # Valore di default se non fornito
            'login_attempts': data.get('total_attempts', data.get('login_attempts', 3)),
            'session_duration': data.get('session_duration_ms', 300000000),  # Default 5 minuti in ms
            'ip_reputation_score': data.get('reputation_score', 0.5),
            'failed_logins': data.get('failed_logins', 0),
            'unusual_time_access': 0  # Calcolato sotto
        }
        
        # Calcola unusual_time_access
        hour = data.get('hour_of_day', datetime.now().hour)
        is_working_hours = data.get('is_working_hours', True)
        feature_mapping['unusual_time_access'] = 1 if (hour < 6 or hour > 22) or not is_working_hours else 0
        
        # Simula network_packet_size basato sul comportamento
        if feature_mapping['failed_logins'] > 2:
            feature_mapping['network_packet_size'] = np.random.randint(500, 800)
        else:
            feature_mapping['network_packet_size'] = np.random.randint(300, 500)
        
        # Crea il DataFrame con le features nell'ordine corretto
        features = ['network_packet_size', 'login_attempts', 'session_duration',
                   'ip_reputation_score', 'failed_logins', 'unusual_time_access']
        
        input_data = [feature_mapping[feat] for feat in features]
        df = pd.DataFrame([input_data], columns=features)
        
        logging.info(f"Features preparate: {dict(zip(features, input_data))}")
        
        # Predizione
        prediction = model.predict(df)[0]
        
        # Determina se è un attaccante
        is_attacker = bool(prediction == 1)
        
        
        # Log del risultato
        if is_attacker:
            logging.warning(f"⚠️ ATTACCO RILEVATO! Utente: {username}")
        else:
            logging.info(f"✓ Utente legittimo: {username}")
        
        
    except Exception as e:
        logging.error(f"Errore durante l'analisi: {str(e)}")
        logging.exception("Dettagli errore:")  # Log completo dell'errore
        return jsonify({
            "error": "Errore durante l'analisi",
            "message": str(e),
            "details": "Controlla i log del server per maggiori dettagli"
        }), 500

# Health check migliorato
@app.route('/api/health', methods=['GET'])
def health():
    health_status = {
        "status": "online",
        "model_loaded": model is not None,
        "model_path": MODEL_PATH,
        "current_directory": os.getcwd(),
        "timestamp": datetime.now().isoformat()
    }
    
    if model is not None:
        health_status["model_info"] = {
            "type": type(model).__name__,
            "has_predict": hasattr(model, 'predict'),
            "has_predict_proba": hasattr(model, 'predict_proba')
        }
    
    return jsonify(health_status)

# Endpoint di test
@app.route('/api/test-prediction', methods=['GET'])
def test_prediction():
    """Test con dati di esempio"""
    test_data = {
        "username": "test_user",
        "failed_logins": 4,
        "total_attempts": 6,
        "reputation_score": 0.3,
        "hour_of_day": 3,
        "is_working_hours": False
    }
    
    logging.info("=== TEST PREDICTION ===")
    return detect_attack()  # Usa la funzione principale

# Ricarica il modello (utile per debug)
@app.route('/api/reload-model', methods=['POST'])
def reload_model():
    """Ricarica il modello dal file"""
    success = load_model()
    return jsonify({
        "success": success,
        "model_loaded": model is not None
    })

# Avvio server
if __name__ == '__main__':
    print("\n" + "="*60)
    print("🛡️  CYBERSECURITY ATTACK DETECTION SERVER")
    print("="*60)
    print(f"📍 Server in ascolto su: http://localhost:5000")
    print(f"📊 Modello: {'✓ Caricato' if model_loaded else '✗ Non caricato'}")
    print(f"📁 Directory corrente: {os.getcwd()}")
    print(f"📄 Cercando modello in: {os.path.join(os.getcwd(), MODEL_PATH)}")
    
    if not model_loaded:
        print("\n⚠️  ATTENZIONE: Il modello non è stato caricato!")
        print("   Assicurati che il file 'cybersecurity_mlp_model.joblib' sia nella stessa")
        print("   directory del server Python.")
        print("\n   Per salvare il modello usa:")
        print("   >>> import joblib")
        print("   >>> joblib.dump(mlp_model, 'cybersecurity_mlp_model.joblib')")
    
    print("\n📌 Endpoints disponibili:")
    print("  POST /api/detect-attack    - Analisi attacco")
    print("  GET  /api/health          - Status del servizio")
    print("  GET  /api/test-prediction - Test con dati esempio")
    print("  POST /api/reload-model    - Ricarica il modello")
    print("="*60 + "\n")
    
    app.run(debug=True, port=5000)