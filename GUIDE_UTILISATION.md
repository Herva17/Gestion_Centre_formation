# 📚 Guide d'Utilisation - Gestion Centre de Formation

## 🚀 Démarrage Rapide

### Installation
1. Extrayez le fichier dans `xampp/htdocs/Gestion_Formation`
2. Importez le fichier SQL dans votre base de données MySQL
3. Accédez à `http://localhost/Gestion_Formation/`

### Configuration de la Base de Données
- **Hôte:** localhost
- **Utilisateur:** root
- **Mot de passe:** (vide par défaut)
- **Base de données:** gestion_centre_formation

Si vos identifiants sont différents, modifiez le fichier `config/Database.php`.

---

## 📖 Modules Disponibles

### 1️⃣ **Dashboard**
- Vue d'ensemble des statistiques
- Graphiques en temps réel
- Métriques clés du centre

**Accès:** Menu Sidebar → Dashboard

### 2️⃣ **Apprenants**
Gérez tous les apprenants de votre centre.

**Fonctionnalités:**
- ➕ Ajouter un nouvel apprenant
- ✏️ Modifier les informations
- 🗑️ Supprimer un apprenant
- 🔍 Rechercher par nom/téléphone

**Champs:**
- Nom*
- Prénom*
- Téléphone
- Adresse

### 3️⃣ **Filières**
Créez et gérez les filières de formation.

**Fonctionnalités:**
- Créer une filière
- Modifier les tarifs mensuels
- Définir la durée
- Vue en cartes

**Champs:**
- Nom de la filière*
- Durée (ex: 6 mois)
- Frais mensuels (en XOF)

### 4️⃣ **Cours**
Organisez les cours par filière.

**Fonctionnalités:**
- Ajouter des cours
- Associer à une filière
- Ajouter une description
- Suppression en cascade des horaires

**Champs:**
- Nom du cours*
- Filière
- Description détaillée

### 5️⃣ **Inscriptions**
Enregistrez les inscriptions des apprenants.

**Fonctionnalités:**
- ➕ Nouvelle inscription
- 📊 Voir les détails
- 💰 Suivi des paiements
- Calcul du solde restant

**Informations capturées:**
- Apprenant*
- Filière*
- Date d'inscription
- Frais d'inscription

### 6️⃣ **Paiements**
Gérez tous les paiements reçus.

**Fonctionnalités:**
- Enregistrer un paiement
- Indiquer le type (espèces, chèque, virement, carte)
- Spécifier le mois
- Historique complet

**Types de paiement:**
- 💵 Espèces
- 📄 Chèque
- 🏦 Virement
- 💳 Carte Bancaire

### 7️⃣ **Salles**
Gérez les salles de cours.

**Fonctionnalités:**
- Ajouter une salle
- Définir la capacité
- Vue en cartes
- Suppression en cascade des horaires

**Champs:**
- Nom de la salle*
- Capacité (nombre de places)*

### 8️⃣ **Horaires**
Planifiez les horaires des cours.

**Fonctionnalités:**
- Créer un horaire
- Sélectionner jour, cours, salle
- Définir heures début/fin
- Modification facile

**Champs:**
- Jour*
- Cours*
- Salle*
- Heure début*
- Heure fin*

---

## 📊 Dashboard - Détails

### Statistiques Principales
- **Total Apprenants:** Nombre d'apprenants enregistrés
- **Total Filières:** Nombre de filières disponibles
- **Inscriptions:** Nombre total d'inscriptions
- **Total Paiements:** Somme de tous les paiements reçus

### Graphiques
1. **Inscriptions par Mois:** Tendance des inscriptions
2. **Revenus par Mois:** Évolution des revenus
3. **Distribution par Filière:** Répartition des apprenants
4. **Résumé:** Données clés

---

## 🎨 Interface Utilisateur

### Barre Latérale (Sidebar)
- Navigation principale
- Lien vers tous les modules
- Menu collapsible sur mobile

### Barre Supérieure
- Titre de la page actuelle
- Notifications
- Profil utilisateur

### Modaux
- Formulaires pour ajouter/modifier
- Validations en temps réel
- Boutons d'action clairs

---

## 💡 Cas d'Usage Courants

### Scénario 1: Inscrire un nouvel apprenant
1. Allez sur **Apprenants** → **Ajouter Apprenant**
2. Remplissez les informations
3. Allez sur **Inscriptions** → **Nouvelle Inscription**
4. Sélectionnez l'apprenant et la filière
5. Enregistrez

### Scénario 2: Suivre un paiement
1. Allez sur **Inscriptions**
2. Cliquez sur l'icône "Voir détails"
3. Cliquez sur **Ajouter Paiement**
4. Entrez le montant et le type
5. Consultez l'historique et le solde

### Scénario 3: Créer un emploi du temps
1. Allez sur **Horaires** → **Ajouter Horaire**
2. Sélectionnez jour, cours, salle
3. Définissez les heures
4. Enregistrez

---

## 🔐 Sécurité

- ✅ Protection contre les injections SQL (PDO)
- ✅ Validation des données côté serveur
- ✅ Headers de sécurité
- ✅ Suppression en cascade des données liées
- ✅ Sessions sécurisées

---

## 🛠️ Maintenance

### Sauvegarde de la Base de Données
```bash
mysqldump -u root gestion_centre_formation > backup.sql
```

### Restauration
```bash
mysql -u root gestion_centre_formation < backup.sql
```

### Logs d'Erreurs
- Voir `error_log` du serveur Apache
- Vérifier la console du navigateur (F12)

---

## 🆘 Dépannage

### Problème: Page blanche
- Vérifiez la connexion à la base de données
- Vérifiez les logs du serveur

### Problème: Les données ne s'affichent pas
- Assurez-vous que la base de données contient des données
- Rafraîchissez la page (Ctrl+F5)

### Problème: Modal ne s'ouvre pas
- Videz le cache du navigateur
- Vérifiez la console JavaScript (F12)

---

## 📞 Support

Pour toute question ou problème:
1. Consultez cette documentation
2. Vérifiez les logs du serveur
3. Testez avec les données d'exemple

---

## 📝 Licence

Application développée pour la gestion des centres de formation.

---

**Dernière mise à jour:** 2024-06-06
**Version:** 1.0.0
