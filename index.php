<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema di Autenticazione - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
            padding: 2rem;
            width: 100%;
            max-width: 1200px;
            margin: 2rem;
        }

        .header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .header h1 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .auth-section {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .login-form, .register-form {
            flex: 1;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .dashboard {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .dashboard.active {
            display: block;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e1e5e9;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .avatar {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .logout-btn {
            padding: 8px 16px;
            background: #dc3545;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #667eea;
        }

        .stat-card h3 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
        }

        .logs-section {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .logs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .logs-table th,
        .logs-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }

        .logs-table th {
            background: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-success {
            background: #d4edda;
            color: #155724;
        }

        .status-failure {
            background: #f8d7da;
            color: #721c24;
        }

        .status-unusual {
            background: #fff3cd;
            color: #856404;
        }

        .reputation-bar {
            width: 100px;
            height: 8px;
            background: #e1e5e9;
            border-radius: 4px;
            overflow: hidden;
        }

        .reputation-fill {
            height: 100%;
            background: linear-gradient(90deg, #dc3545, #ffc107, #28a745);
            transition: width 0.3s ease;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-weight: 500;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 768px) {
            .auth-section {
                flex-direction: column;
            }
            
            .container {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistema di Autenticazione</h1>
            <p>Dashboard con monitoraggio avanzato degli utenti</p>
        </div>

        <!-- Sezione Autenticazione -->
        <div id="authSection" class="auth-section">
            <!-- Form Login -->
            <div class="login-form">
                <h2>Accedi</h2>
                <div id="loginAlert"></div>
                <form id="loginForm">
                    <div class="form-group">
                        <label for="loginUsername">Username</label>
                        <input type="text" id="loginUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="loginPassword">Password</label>
                        <input type="password" id="loginPassword" required>
                    </div>
                    <button type="submit" class="btn">Accedi</button>
                </form>
            </div>

            <!-- Form Registrazione -->
            <div class="register-form">
                <h2>Registrati</h2>
                <div id="registerAlert"></div>
                <form id="registerForm">
                    <div class="form-group">
                        <label for="registerUsername">Username</label>
                        <input type="text" id="registerUsername" required>
                    </div>
                    <div class="form-group">
                        <label for="registerEmail">Email</label>
                        <input type="email" id="registerEmail" required>
                    </div>
                    <div class="form-group">
                        <label for="registerPassword">Password</label>
                        <input type="password" id="registerPassword" required>
                    </div>
                    <button type="submit" class="btn">Registrati</button>
                </form>
            </div>
        </div>

        <!-- Dashboard -->
        <div id="dashboard" class="dashboard">
            <div class="dashboard-header">
                <div class="user-info">
                    <div class="avatar" id="userAvatar">U</div>
                    <div>
                        <h3 id="welcomeMessage">Benvenuto, Utente!</h3>
                        <p>Ultima sessione: <span id="lastSession">-</span></p>
                    </div>
                </div>
                <button class="logout-btn" onclick="logout()">Logout</button>
            </div>

            <!-- Statistiche -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Login Falliti</h3>
                    <div class="value" id="failedLogins">0</div>
                    <button onclick="saveStatsToFile(currentUser)" style="margin-top: 10px; padding: 6px 12px; background: #667eea; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.875rem;">Salva Stats</button>
                </div>
                <div class="stat-card">
                    <h3>Reputation Score</h3>
                    <div class="value" id="reputationScore">0.5</div>
                    <div class="reputation-bar">
                        <div class="reputation-fill" id="reputationFill" style="width: 50%"></div>
                    </div>
                </div>
                <div class="stat-card">
                    <h3>Durata Sessione</h3>
                    <div class="value" id="sessionDuration">0m</div>
                </div>
                <div class="stat-card">
                    <h3>Pacchetti Trasmessi</h3>
                    <div class="value" id="packetSize">0 KB</div>
                </div>
                <div class="stat-card">
                    <h3>Tentativi Login</h3>
                    <div class="value" id="loginAttempts">0</div>
                </div>
                <div class="stat-card">
                    <h3>Orario Accesso</h3>
                    <div class="value" id="accessTime">Normale</div>
                </div>
            </div>

            <!-- Log delle Attività -->
            <div class="logs-section">
                <h2>Log delle Attività</h2>
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Azione</th>
                            <th>Status</th>
                            <th>IP</th>
                            <th>Durata</th>
                            <th>Pacchetti</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody">
                        <!-- I log verranno inseriti dinamicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Simulazione del database utenti
        let users = {
            'admin': {
                password: 'admin123',
                email: 'admin@example.com',
                failedLogins: 0,
                reputationScore: 0.8,
                loginAttempts: 5,
                lastSession: null,
                logs: []
            }
        };
        
        let currentUser = null;
        let sessionStart = null;
        let packetCounter = 0;
        let sessionInterval = null;
        let packetInterval = null;

        // Simulazione del traffico di pacchetti
        function simulatePacketTraffic() {
            if (currentUser) {
                packetCounter += Math.floor(Math.random() * 10) + 1;
                document.getElementById('packetSize').textContent = `${packetCounter} KB`;
            }
        }

        // Aggiornamento durata sessione
        function updateSessionDuration() {
            if (sessionStart) {
                const duration = Math.floor((Date.now() - sessionStart) / 1000);
                const minutes = Math.floor(duration / 60);
                const seconds = duration % 60;
                document.getElementById('sessionDuration').textContent = `${minutes}m ${seconds}s`;
            }
        }

        // Verifica se l'orario è lavorativo (9-18)
        function isWorkingHours() {
            const hour = new Date().getHours();
            return hour >= 9 && hour <= 18;
        }

        // Genera IP casuale per simulazione
        function generateRandomIP() {
            return `${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}.${Math.floor(Math.random() * 256)}`;
        }

        // Aggiungi log di attività
        function addLog(action, status, duration = '-', username = null) {
            const log = {
                timestamp: new Date().toLocaleString('it-IT'),
                action: action,
                status: status,
                ip: generateRandomIP(),
                duration: duration,
                packets: `${Math.floor(Math.random() * 50) + 10} KB`
            };

            const targetUser = username || currentUser;
            if (targetUser && users[targetUser]) {
                users[targetUser].logs.unshift(log);
                if (targetUser === currentUser) {
                    updateLogsTable();
                }
            }
        }

        // Aggiorna tabella dei log
        function updateLogsTable() {
            const tbody = document.getElementById('logsTableBody');
            tbody.innerHTML = '';

            if (currentUser && users[currentUser]) {
                users[currentUser].logs.slice(0, 10).forEach(log => {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>${log.timestamp}</td>
                        <td>${log.action}</td>
                        <td><span class="status-badge status-${log.status}">${log.status === 'success' ? 'Successo' : log.status === 'failure' ? 'Fallimento' : 'Inusuale'}</span></td>
                        <td>${log.ip}</td>
                        <td>${log.duration}</td>
                        <td>${log.packets}</td>
                    `;
                });
            }
        }

        // Aggiorna statistiche dashboard
        function updateDashboard() {
            if (currentUser && users[currentUser]) {
                const user = users[currentUser];
                
                document.getElementById('welcomeMessage').textContent = `Benvenuto, ${currentUser}!`;
                document.getElementById('userAvatar').textContent = currentUser.charAt(0).toUpperCase();
                document.getElementById('failedLogins').textContent = user.failedLogins || 0;
                document.getElementById('reputationScore').textContent = user.reputationScore.toFixed(2);
                document.getElementById('reputationFill').style.width = `${user.reputationScore * 100}%`;
                document.getElementById('loginAttempts').textContent = user.loginAttempts || 0;
                document.getElementById('accessTime').textContent = isWorkingHours() ? 'Normale' : 'Inusuale';
                
                if (user.lastSession) {
                    document.getElementById('lastSession').textContent = new Date(user.lastSession).toLocaleString('it-IT');
                }
                
                updateLogsTable();
            }
        }

        // Sistema di logging interno (simula file di log)
        let systemLogs = [];
        const MAX_LOG_ENTRIES = 1000; // Limite per evitare memory leak
        
        // URL del server Python (modificare con il tuo URL)
        const PYTHON_SERVER_URL = 'http://localhost:5000/api/detect-attack';

        // Funzione per inviare dati al server Python
        async function sendToPythonML(userData) {
            try {
                const response = await fetch(PYTHON_SERVER_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(userData)
                });
                
                if (response.ok) {
                    const result = await response.json();
                    console.log('Risultato Detection:', result);
                    
                    // Mostra il risultato all'utente
                    showAttackDetectionResult(result);
                    
                    return result;
                } else {
                    console.error('Errore invio dati:', response.statusText);
                }
            } catch (error) {
                console.error('Errore connessione server Python:', error);
                // Continua a funzionare anche se il server Python non è disponibile
            }
        }
        
        // Funzione per mostrare risultato attack detection
        function showAttackDetectionResult(result) {
            // Crea un elemento per mostrare il risultato se non esiste
            if (!document.getElementById('attackDetection')) {
                const detectionCard = document.createElement('div');
                detectionCard.className = 'stat-card';
                detectionCard.innerHTML = `
                    <h3>Attack Detection</h3>
                    <div class="value" id="attackStatus" style="font-size: 1.5rem;">-</div>
                    <div class="reputation-bar" style="margin-top: 10px;">
                        <div class="reputation-fill" id="attackConfidence" style="width: 0%; transition: width 0.5s ease;"></div>
                    </div>
                    <p style="font-size: 0.8rem; color: #666; margin-top: 0.5rem;" id="attackMessage"></p>
                `;
                document.querySelector('.stats-grid').appendChild(detectionCard);
            }
            
            // Aggiorna i valori
            const statusEl = document.getElementById('attackStatus');
            const confidenceEl = document.getElementById('attackConfidence');
            const messageEl = document.getElementById('attackMessage');
            
            if (result.is_attacker) {
                statusEl.textContent = '⚠️ ATTACCANTE';
                statusEl.style.color = '#ef4444';
                confidenceEl.style.background = '#ef4444';
            } else {
                statusEl.textContent = '✓ LEGITTIMO';
                statusEl.style.color = '#10b981';
                confidenceEl.style.background = '#10b981';
            }
            
            confidenceEl.style.width = `${result.confidence * 100}%`;
            messageEl.textContent = `Rischio: ${result.risk_level} (${(result.risk_probability * 100).toFixed(1)}%)`;
        }

        // Funzione per aggiungere entry al log di sistema
        function writeToSystemLog(action, data) {
            const logEntry = {
                timestamp: new Date().toISOString(),
                action: action,
                data: data,
                sessionId: Math.random().toString(36).substr(2, 9)
            };
            
            systemLogs.push(logEntry);
            
            // Mantieni solo gli ultimi MAX_LOG_ENTRIES
            if (systemLogs.length > MAX_LOG_ENTRIES) {
                systemLogs = systemLogs.slice(-MAX_LOG_ENTRIES);
            }
            
            // Salva in localStorage (persiste tra le sessioni)
            try {
                localStorage.setItem('systemLogs', JSON.stringify(systemLogs));
            } catch (e) {
                console.error('Impossibile salvare logs:', e);
            }
            
            // Log anche nella console per debug
            console.log('[SYSTEM LOG]', logEntry);
        }

        // Carica logs esistenti da localStorage
        function loadSystemLogs() {
            try {
                const saved = localStorage.getItem('systemLogs');
                if (saved) {
                    systemLogs = JSON.parse(saved);
                }
            } catch (e) {
                console.error('Impossibile caricare logs:', e);
            }
        }

        // Funzione per salvare le statistiche nel log interno
        function logUserStats(username, event) {
            if (!users[username]) return;
            
            // Calcola la durata della sessione se disponibile
            let sessionDurationMs = 0;
            if (sessionStart) {
                sessionDurationMs = Date.now() - sessionStart;
            } else {
                // Simula una durata casuale per test
                sessionDurationMs = Math.floor(Math.random() * 600000000) + 30000000;
            }
            
            const stats = {
                username: username,
                event: event,
                loginStats: {
                    failedLogins: users[username].failedLogins || 0,
                    totalAttempts: users[username].loginAttempts || 0,
                    reputationScore: users[username].reputationScore
                },
                sessionInfo: {
                    accessTime: isWorkingHours() ? 'Orario lavorativo' : 'Orario inusuale',
                    ip: generateRandomIP(),
                    userAgent: navigator.userAgent,
                    timestamp: new Date().toISOString(),
                    hour: new Date().getHours()
                }
            };
            
            writeToSystemLog(event, stats);
            
            // Invia i dati al server Python per attack detection
            sendToPythonML({
                username: username,
                event: event,
                failed_logins: stats.loginStats.failedLogins,
                total_attempts: stats.loginStats.totalAttempts,
                login_attempts: stats.loginStats.totalAttempts,  // alias per compatibilità
                reputation_score: stats.loginStats.reputationScore,
                is_working_hours: isWorkingHours(),
                hour_of_day: new Date().getHours(),
                day_of_week: new Date().getDay(),
                session_duration_ms: sessionDurationMs,
                user_agent: navigator.userAgent,
                timestamp: stats.sessionInfo.timestamp
            });
        }

        // Funzione per esportare tutti i logs (solo per admin/debug)
        function exportSystemLogs() {
            const logsText = systemLogs.map(entry => {
                return `[${entry.timestamp}] ${entry.action} - ${JSON.stringify(entry.data)}`;
            }).join('\n');
            
            return logsText;
        }

        // Funzione per visualizzare i logs nel sistema (nascosta all'utente normale)
        function viewSystemLogs(filter = null) {
            let logs = systemLogs;
            if (filter) {
                logs = systemLogs.filter(log => 
                    log.action.includes(filter) || 
                    JSON.stringify(log.data).includes(filter)
                );
            }
            console.table(logs);
            return logs;
        }

        // Inizializza il sistema di logging
        loadSystemLogs();

        // Funzione per salvare le statistiche su file (mantenuta per compatibilità)
        function saveStatsToFile(username) {
            if (!users[username]) return;
            
            const stats = {
                username: username,
                timestamp: new Date().toISOString(),
                loginStats: {
                    failedLogins: users[username].failedLogins || 0,
                    totalAttempts: users[username].loginAttempts || 0,
                    lastSession: users[username].lastSession,
                    reputationScore: users[username].reputationScore
                },
                logs: users[username].logs || [],
                sessionInfo: {
                    accessTime: isWorkingHours() ? 'Orario lavorativo' : 'Orario inusuale',
                    currentIP: generateRandomIP()
                }
            };
            
            // Crea il blob con i dati JSON
            const dataStr = JSON.stringify(stats, null, 2);
            const dataBlob = new Blob([dataStr], { type: 'application/json' });
            
            // Crea un link temporaneo per il download
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = `stats_${username}_${new Date().toISOString().replace(/[:.]/g, '-')}.json`;
            
            // Trigger del download
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            console.log(`Statistiche salvate per l'utente ${username}`);
        }

        // Funzione per visualizzare statistiche di qualsiasi utente (per debug)
        function showUserStats(username) {
            if (users[username]) {
                console.log(`=== Statistiche per ${username} ===`);
                console.log(`Login falliti: ${users[username].failedLogins || 0}`);
                console.log(`Tentativi totali: ${users[username].loginAttempts || 0}`);
                console.log(`Reputation: ${users[username].reputationScore.toFixed(3)}`);
                console.log(`Logs: ${users[username].logs.length}`);
                return users[username];
            }
            return null;
        }

        // Gestione login - VERSIONE CORRETTA
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('loginUsername').value;
            const password = document.getElementById('loginPassword').value;
            const alertDiv = document.getElementById('loginAlert');
            
            // Incrementa sempre i tentativi di login se l'utente esiste
            if (users[username]) {
                users[username].loginAttempts = (users[username].loginAttempts || 0) + 1;
                
                if (users[username].password === password) {
                    // Login riuscito
                    currentUser = username;
                    sessionStart = Date.now();
                    packetCounter = 0;
                    
                    users[username].lastSession = Date.now();
                    
                    // Salva il numero di login falliti prima di resettarlo
                    const previousFailedLogins = users[username].failedLogins || 0;
                    
                    // Log automatico delle statistiche prima del reset
                    if (previousFailedLogins > 0) {
                        logUserStats(username, 'LOGIN_SUCCESS_AFTER_FAILURES');
                    } else {
                        logUserStats(username, 'LOGIN_SUCCESS');
                    }
                    
                    // Aggiorna reputation score positivamente
                    users[username].reputationScore = Math.min(1, users[username].reputationScore + 0.1);
                    
                    document.getElementById('authSection').style.display = 'none';
                    document.getElementById('dashboard').classList.add('active');
                    
                    alertDiv.innerHTML = '<div class="alert alert-success">Login effettuato con successo!</div>';
                    
                    addLog('Login', 'success', '-', username);
                    
                    // Aggiorna dashboard PRIMA di resettare failedLogins
                    updateDashboard();
                    
                    // Resetta login falliti DOPO aver aggiornato il dashboard
                    setTimeout(() => {
                        users[username].failedLogins = 0;
                        // Se vuoi mantenere una cronologia, potresti aggiungerla qui
                        users[username].failedLoginsHistory = users[username].failedLoginsHistory || [];
                        if (previousFailedLogins > 0) {
                            users[username].failedLoginsHistory.push({
                                count: previousFailedLogins,
                                resetAt: new Date().toISOString()
                            });
                        }
                    }, 100);
                    
                    // Avvia i timer per aggiornamenti
                    sessionInterval = setInterval(updateSessionDuration, 1000);
                    packetInterval = setInterval(simulatePacketTraffic, 2000);
                    
                } else {
                    // Login fallito - password errata
                    users[username].failedLogins = (users[username].failedLogins || 0) + 1;
                    
                    // Log automatico del tentativo fallito
                    logUserStats(username, 'LOGIN_FAILED_WRONG_PASSWORD');
                    
                    // Diminuisci reputation score
                    users[username].reputationScore = Math.max(0, users[username].reputationScore - 0.05);
                    
                    alertDiv.innerHTML = `<div class="alert alert-danger">Password errata! Login falliti: ${users[username].failedLogins}</div>`;
                    addLog('Login Fallito - Password Errata', 'failure', '-', username);
                    
                    // Se l'utente è quello corrente nel dashboard, aggiorna le statistiche
                    if (currentUser === username) {
                        updateDashboard();
                    }
                }
            } else {
                // Username non trovato
                logUserStats(username || 'UNKNOWN', 'LOGIN_FAILED_USER_NOT_FOUND');
                alertDiv.innerHTML = '<div class="alert alert-danger">Username non trovato!</div>';
                addLog('Login Fallito - Username Inesistente', 'failure');
            }
            
            // Clear alert dopo 5 secondi
            setTimeout(() => {
                alertDiv.innerHTML = '';
            }, 5000);
            
            // Reset form
            document.getElementById('loginForm').reset();
        });

        // Gestione registrazione
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('registerUsername').value;
            const email = document.getElementById('registerEmail').value;
            const password = document.getElementById('registerPassword').value;
            const alertDiv = document.getElementById('registerAlert');
            
            if (users[username]) {
                alertDiv.innerHTML = '<div class="alert alert-danger">Username già esistente!</div>';
            } else {
                users[username] = {
                    password: password,
                    email: email,
                    failedLogins: 0,
                    reputationScore: 0.5,
                    loginAttempts: 0,
                    lastSession: null,
                    logs: []
                };
                
                alertDiv.innerHTML = '<div class="alert alert-success">Registrazione completata! Ora puoi effettuare il login.</div>';
                document.getElementById('registerForm').reset();
            }
            
            setTimeout(() => {
                alertDiv.innerHTML = '';
            }, 3000);
        });

        // Logout
        function logout() {
            if (currentUser && sessionStart) {
                const sessionDuration = Math.floor((Date.now() - sessionStart) / 1000);
                const minutes = Math.floor(sessionDuration / 60);
                const seconds = sessionDuration % 60;
                addLog('Logout', 'success', `${minutes}m ${seconds}s`);
                
                // Log automatico del logout
                logUserStats(currentUser, 'LOGOUT');
            }
            
            // Clear intervals
            if (sessionInterval) clearInterval(sessionInterval);
            if (packetInterval) clearInterval(packetInterval);
            
            currentUser = null;
            sessionStart = null;
            packetCounter = 0;
            
            document.getElementById('authSection').style.display = 'flex';
            document.getElementById('dashboard').classList.remove('active');
            
            // Reset form
            document.getElementById('loginForm').reset();
        }

        // Inizializza con alcuni log di esempio per l'admin
        users.admin.logs = [
            {
                timestamp: new Date(Date.now() - 3600000).toLocaleString('it-IT'),
                action: 'Login',
                status: 'success',
                ip: '192.168.1.100',
                duration: '45m 30s',
                packets: '1.2 MB'
            },
            {
                timestamp: new Date(Date.now() - 7200000).toLocaleString('it-IT'),
                action: 'Login Fallito',
                status: 'failure',
                ip: '192.168.1.101',
                duration: '-',
                packets: '2 KB'
            }
        ];

        // Debug helper - puoi usare questa funzione nella console
        window.debugUser = showUserStats;
        
        // Funzioni admin per gestire i logs di sistema (accessibili via console)
        window.systemLogManager = {
            view: viewSystemLogs,
            export: exportSystemLogs,
            clear: () => {
                systemLogs = [];
                localStorage.removeItem('systemLogs');
                console.log('System logs cleared');
            },
            count: () => systemLogs.length
        };
    </script>
</body>
</html>