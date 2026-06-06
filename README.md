# Gestion Centre de Formation

## 📋 Description
Application web moderne de gestion pour un centre de formation avec:
- Dashboard intuitif avec statistiques
- Gestion des apprenants
- Gestion des filières
- Gestion des cours
- Gestion des inscriptions
- Gestion des paiements
- Gestion des salles
- Gestion des horaires

## 🎨 Design
- Interface moderne avec **Tailwind CSS**
- Thème dégradé professionnels (violet, bleu, orange)
- Responsive et mobile-friendly
- Charts.js pour les statistiques

## 🚀 Installation

1. Placer le dossier dans `xampp/htdocs/Gestion_Formation`
2. Créer la base de données MySQL avec le fichier SQL
3. Accéder à `http://localhost/Gestion_Formation/`

## 📁 Structure

```
Gestion_Formation/
├── config/
│   └── Database.php
├── classes/
│   ├── Apprenant.php
│   ├── Filiere.php
│   ├── Inscription.php
│   ├── Paiement.php
│   ├── Cours.php
│   ├── Salle.php
│   ├── Horaire.php
├── pages/
│   ├── dashboard.php
│   ├── apprenants.php
│   ├── filieres.php
│   ├── inscriptions.php
│   ├── paiements.php
│   ├── cours.php
│   ├── salles.php
│   ├── horaires.php
│   └── inscription-detail.php
├── includes/
│   ├── header.php
│   └── footer.php
└── index.php
```

## 🛠️ Technologies
- PHP 8+
- MySQL
- Tailwind CSS
- Chart.js
- Font Awesome Icons
- PDO (Base de données)

## ✨ Fonctionnalités
- ✅ CRUD complets pour tous les modules
- ✅ Dashboard avec statistiques en temps réel
- ✅ Graphiques interactifs
- ✅ Recherche et filtrage
- ✅ Design responsive
- ✅ Gestion des erreurs
- ✅ Sessions utilisateur

## 👤 Auteur
Application générée pour la gestion efficace des centres de formation.
