<?php
session_start();
$page_title = 'Documentation API';
include __DIR__ . '/../includes/header.php';
?>

                <!-- Documentation -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-3xl font-bold text-gray-800 mb-6">Documentation API</h2>

                    <div class="prose max-w-none">
                        <!-- Introduction -->
                        <div class="mb-8 p-6 bg-blue-50 rounded-lg border-l-4 border-blue-500">
                            <h3 class="text-xl font-bold text-gray-800 mb-2">Introduction</h3>
                            <p class="text-gray-700">
                                L'API REST de GCF permet d'accéder aux données de votre centre de formation. 
                                Tous les endpoints retournent des données au format JSON.
                            </p>
                        </div>

                        <!-- Base URL -->
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-4">URL de Base</h3>
                            <div class="bg-gray-900 text-white p-4 rounded-lg font-mono">
                                <code>http://localhost/Gestion_Formation/api/</code>
                            </div>
                        </div>

                        <!-- Endpoints -->
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Endpoints</h3>

                            <!-- GET Apprenants -->
                            <div class="mb-6 p-6 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                <h4 class="text-lg font-bold text-gray-800 mb-2">
                                    <span class="bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">GET</span>
                                    /api/apprenants
                                </h4>
                                <p class="text-gray-700 mb-3">Récupère tous les apprenants</p>
                                <div class="bg-white p-4 rounded border border-gray-300 font-mono text-sm">
                                    <div class="text-gray-600">Réponse:</div>
                                    <pre class="text-gray-800">{
  "success": true,
  "data": [...],
  "count": 10
}</pre>
                                </div>
                            </div>

                            <!-- GET Filieres -->
                            <div class="mb-6 p-6 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                <h4 class="text-lg font-bold text-gray-800 mb-2">
                                    <span class="bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">GET</span>
                                    /api/filieres
                                </h4>
                                <p class="text-gray-700 mb-3">Récupère toutes les filières</p>
                            </div>

                            <!-- GET Inscriptions -->
                            <div class="mb-6 p-6 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                <h4 class="text-lg font-bold text-gray-800 mb-2">
                                    <span class="bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">GET</span>
                                    /api/inscriptions
                                </h4>
                                <p class="text-gray-700 mb-3">Récupère toutes les inscriptions</p>
                            </div>

                            <!-- GET Paiements -->
                            <div class="mb-6 p-6 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                <h4 class="text-lg font-bold text-gray-800 mb-2">
                                    <span class="bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">GET</span>
                                    /api/paiements
                                </h4>
                                <p class="text-gray-700 mb-3">Récupère tous les paiements</p>
                            </div>

                            <!-- GET Statistiques -->
                            <div class="mb-6 p-6 bg-gray-50 rounded-lg border-l-4 border-green-500">
                                <h4 class="text-lg font-bold text-gray-800 mb-2">
                                    <span class="bg-green-600 text-white px-3 py-1 rounded text-sm mr-2">GET</span>
                                    /api/statistiques
                                </h4>
                                <p class="text-gray-700 mb-3">Récupère les statistiques générales</p>
                                <div class="bg-white p-4 rounded border border-gray-300 font-mono text-sm">
                                    <div class="text-gray-600">Réponse:</div>
                                    <pre class="text-gray-800">{
  "success": true,
  "data": {
    "total_apprenants": 42,
    "total_filieres": 8,
    "total_inscriptions": 156,
    "total_paiements": 1250000,
    "total_revenue": 2500000,
    "revenue_inscriptions": 650000
  }
}</pre>
                                </div>
                            </div>
                        </div>

                        <!-- Codes de Réponse -->
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Codes de Réponse</h3>
                            <table class="w-full border-collapse">
                                <thead class="bg-gray-800 text-white">
                                    <tr>
                                        <th class="px-4 py-2 text-left">Code</th>
                                        <th class="px-4 py-2 text-left">Signification</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-bold">200</td>
                                        <td class="px-4 py-2">Succès</td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="px-4 py-2 font-bold">404</td>
                                        <td class="px-4 py-2">Non trouvé</td>
                                    </tr>
                                    <tr>
                                        <td class="px-4 py-2 font-bold">500</td>
                                        <td class="px-4 py-2">Erreur serveur</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Exemples -->
                        <div class="mb-8">
                            <h3 class="text-2xl font-bold text-gray-800 mb-4">Exemples d'Utilisation</h3>

                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3">JavaScript (Fetch API)</h4>
                                <div class="bg-gray-900 text-white p-4 rounded-lg font-mono text-sm overflow-x-auto">
                                    <pre>fetch('/Gestion_Formation/api/statistiques')
  .then(response => response.json())
  .then(data => console.log(data))
  .catch(error => console.error(error));</pre>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3">cURL</h4>
                                <div class="bg-gray-900 text-white p-4 rounded-lg font-mono text-sm overflow-x-auto">
                                    <pre>curl -X GET http://localhost/Gestion_Formation/api/apprenants</pre>
                                </div>
                            </div>

                            <div class="mb-6">
                                <h4 class="text-lg font-bold text-gray-800 mb-3">Python</h4>
                                <div class="bg-gray-900 text-white p-4 rounded-lg font-mono text-sm overflow-x-auto">
                                    <pre>import requests

url = 'http://localhost/Gestion_Formation/api/statistiques'
response = requests.get(url)
data = response.json()
print(data)</pre>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="p-6 bg-yellow-50 rounded-lg border-l-4 border-yellow-500">
                            <h3 class="text-lg font-bold text-gray-800 mb-2">Notes Importantes</h3>
                            <ul class="list-disc list-inside text-gray-700 space-y-2">
                                <li>Toutes les réponses sont au format JSON</li>
                                <li>Aucune authentification requise actuellement</li>
                                <li>Les données retournées incluent tous les enregistrements</li>
                                <li>Les erreurs retournent également du JSON avec "success": false</li>
                            </ul>
                        </div>
                    </div>
                </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
