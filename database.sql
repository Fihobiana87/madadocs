-- ============================================================
-- MadaDocs — schéma MySQL / MariaDB (compatible InfinityFree)
-- Importer ce fichier via phpMyAdmin dans la base créée sur
-- le panneau InfinityFree.
-- ============================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------
-- users
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    email VARCHAR(180) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- categories
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(60) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(180) NOT NULL DEFAULT '',
    icon VARCHAR(10) NOT NULL DEFAULT '📄',
    position SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- documents (modèles de documents / templates)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    slug VARCHAR(80) NOT NULL,
    name VARCHAR(120) NOT NULL,
    description VARCHAR(220) NOT NULL DEFAULT '',
    keywords VARCHAR(255) NOT NULL DEFAULT '',
    pdf_view VARCHAR(30) NOT NULL DEFAULT 'letter',
    fields_schema TEXT NOT NULL,
    subject_template VARCHAR(255) NULL,
    body_template TEXT NULL,
    usage_count INT UNSIGNED NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_documents_slug (slug),
    KEY idx_documents_category (category_id),
    CONSTRAINT fk_documents_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- generated_documents (historique des documents générés)
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS generated_documents (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,
    document_id INT UNSIGNED NOT NULL,
    file_name VARCHAR(180) NOT NULL,
    data_json TEXT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_generated_user (user_id),
    KEY idx_generated_document (document_id),
    CONSTRAINT fk_generated_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
    CONSTRAINT fk_generated_document FOREIGN KEY (document_id) REFERENCES documents (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------
-- favorites
-- ---------------------------------------------------------------
CREATE TABLE IF NOT EXISTS favorites (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    document_id INT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_favorite (user_id, document_id),
    CONSTRAINT fk_favorite_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
    CONSTRAINT fk_favorite_document FOREIGN KEY (document_id) REFERENCES documents (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- Données de départ
-- ============================================================

INSERT INTO categories (slug, name, description, icon, position) VALUES
('emploi', 'Emploi', 'CV, lettres et demandes d’embauche', '💼', 1),
('stage', 'Stage', 'Demandes de stage et candidatures', '🎓', 2),
('administratif', 'Administratif', 'Congés, permissions et démarches officielles', '🖋️', 3),
('entreprise', 'Entreprise', 'Factures, devis et attestations', '🧾', 4);

-- ---------- Emploi ----------

INSERT INTO documents (category_id, slug, name, description, keywords, pdf_view, fields_schema, subject_template, body_template) VALUES
(
  (SELECT id FROM categories WHERE slug = 'emploi'),
  'cv',
  'CV',
  'Un CV clair et professionnel, prêt à envoyer.',
  'cv curriculum vitae emploi experience',
  'cv',
  '[
    {"name":"nom","label":"Nom complet","type":"text","required":true,"placeholder":"Rakoto Jean"},
    {"name":"titre","label":"Titre / poste recherché","type":"text","required":true,"placeholder":"Développeur web"},
    {"name":"email","label":"Email","type":"email","required":true},
    {"name":"telephone","label":"Téléphone","type":"text","required":true,"placeholder":"034 00 000 00"},
    {"name":"adresse","label":"Ville, adresse","type":"text","required":false,"placeholder":"Antananarivo, Madagascar"},
    {"name":"profil","label":"Profil / accroche","type":"textarea","required":false,"placeholder":"Deux à trois phrases sur votre profil professionnel."},
    {"name":"experience_1","label":"Expérience 1 (poste, entreprise, dates)","type":"text","required":false},
    {"name":"experience_1_detail","label":"Détail de l’expérience 1","type":"textarea","required":false},
    {"name":"experience_2","label":"Expérience 2 (poste, entreprise, dates)","type":"text","required":false},
    {"name":"experience_2_detail","label":"Détail de l’expérience 2","type":"textarea","required":false},
    {"name":"formation_1","label":"Formation 1 (diplôme, établissement, année)","type":"text","required":false},
    {"name":"formation_2","label":"Formation 2 (diplôme, établissement, année)","type":"text","required":false},
    {"name":"competences","label":"Compétences (séparées par des virgules)","type":"textarea","required":false},
    {"name":"langues","label":"Langues","type":"text","required":false,"placeholder":"Malagasy, Français, Anglais"}
  ]',
  NULL,
  NULL
),
(
  (SELECT id FROM categories WHERE slug = 'emploi'),
  'lettre-motivation',
  'Lettre de motivation',
  'Une lettre de motivation adaptée au poste visé.',
  'lettre motivation candidature emploi poste',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"adresse","label":"Votre adresse","type":"text","required":false},
    {"name":"ville","label":"Ville","type":"text","required":true,"placeholder":"Antananarivo"},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire (nom ou service)","type":"text","required":true,"placeholder":"Madame, Monsieur le Responsable RH"},
    {"name":"entreprise","label":"Entreprise","type":"text","required":true},
    {"name":"poste","label":"Poste visé","type":"text","required":true},
    {"name":"motivation","label":"Pourquoi ce poste vous intéresse","type":"textarea","required":true},
    {"name":"atouts","label":"Vos atouts pour le poste","type":"textarea","required":true}
  ]',
  'Candidature au poste de {{poste}}',
  'Objet : Candidature au poste de {{poste}}\n\n{{destinataire}},\n\nJe me permets de vous adresser ma candidature pour le poste de {{poste}} au sein de {{entreprise}}.\n\n{{motivation}}\n\n{{atouts}}\n\nJe reste à votre disposition pour un entretien et vous remercie de l’attention portée à ma candidature.\n\nVeuillez agréer, {{destinataire}}, l’expression de mes salutations distinguées.'
),
(
  (SELECT id FROM categories WHERE slug = 'emploi'),
  'demande-emploi',
  'Demande d’emploi',
  'Demande formelle d’embauche auprès d’une entreprise.',
  'demande emploi embauche travail',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"adresse","label":"Votre adresse","type":"text","required":false},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur le Directeur"},
    {"name":"entreprise","label":"Entreprise","type":"text","required":true},
    {"name":"poste","label":"Poste souhaité","type":"text","required":true},
    {"name":"profil","label":"Présentation de votre profil","type":"textarea","required":true}
  ]',
  'Demande d’emploi',
  'Objet : Demande d’emploi\n\n{{destinataire}},\n\nJe me permets de solliciter un emploi au sein de {{entreprise}}, au poste de {{poste}}.\n\n{{profil}}\n\nJe me tiens à votre disposition pour tout complément d’information ou un entretien.\n\nDans l’attente de votre réponse favorable, veuillez agréer, {{destinataire}}, mes salutations respectueuses.'
);

-- ---------- Stage ----------

INSERT INTO documents (category_id, slug, name, description, keywords, pdf_view, fields_schema, subject_template, body_template) VALUES
(
  (SELECT id FROM categories WHERE slug = 'stage'),
  'demande-stage',
  'Demande de stage',
  'Sollicitez un stage dans l’entreprise de votre choix.',
  'demande stage entreprise etudiant',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"etablissement","label":"Établissement / formation","type":"text","required":true},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur"},
    {"name":"entreprise","label":"Entreprise d’accueil","type":"text","required":true},
    {"name":"domaine","label":"Domaine du stage","type":"text","required":true,"placeholder":"informatique, comptabilité..."},
    {"name":"duree","label":"Durée souhaitée","type":"text","required":true,"placeholder":"2 mois, à partir du..."},
    {"name":"motivation","label":"Pourquoi ce stage vous intéresse","type":"textarea","required":true}
  ]',
  'Demande de stage en {{domaine}}',
  'Objet : Demande de stage en {{domaine}}\n\n{{destinataire}},\n\nActuellement en formation à {{etablissement}}, je souhaite effectuer un stage de {{duree}} au sein de {{entreprise}}.\n\n{{motivation}}\n\nJe reste à votre entière disposition pour échanger sur cette demande.\n\nVeuillez agréer, {{destinataire}}, l’expression de mes salutations distinguées.'
),
(
  (SELECT id FROM categories WHERE slug = 'stage'),
  'candidature-spontanee',
  'Candidature spontanée',
  'Proposez vos services sans offre publiée.',
  'candidature spontanee entreprise emploi stage',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur"},
    {"name":"entreprise","label":"Entreprise","type":"text","required":true},
    {"name":"domaine","label":"Domaine visé","type":"text","required":true},
    {"name":"profil","label":"Votre profil et motivations","type":"textarea","required":true}
  ]',
  'Candidature spontanée',
  'Objet : Candidature spontanée\n\n{{destinataire}},\n\nVivement intéressé(e) par les activités de {{entreprise}} dans le domaine de {{domaine}}, je me permets de vous adresser ma candidature spontanée.\n\n{{profil}}\n\nJe reste disponible pour un entretien à votre convenance.\n\nVeuillez agréer, {{destinataire}}, mes salutations distinguées.'
);

-- ---------- Administratif ----------

INSERT INTO documents (category_id, slug, name, description, keywords, pdf_view, fields_schema, subject_template, body_template) VALUES
(
  (SELECT id FROM categories WHERE slug = 'administratif'),
  'demande-permission',
  'Demande de permission',
  'Demandez une autorisation d’absence ponctuelle.',
  'demande permission absence autorisation',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"fonction","label":"Fonction / classe","type":"text","required":false},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date de la lettre","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur le Directeur"},
    {"name":"date_permission","label":"Date(s) concernée(s)","type":"text","required":true},
    {"name":"duree","label":"Durée","type":"text","required":true,"placeholder":"1 journée, demi-journée..."},
    {"name":"motif","label":"Motif","type":"textarea","required":true}
  ]',
  'Demande de permission',
  'Objet : Demande de permission\n\n{{destinataire}},\n\nJe soussigné(e) {{nom}}, sollicite une autorisation d’absence de {{duree}} le {{date_permission}}.\n\nMotif : {{motif}}\n\nJe vous remercie de l’attention que vous porterez à ma demande et reste disponible pour tout complément d’information.\n\nVeuillez agréer, {{destinataire}}, mes salutations respectueuses.'
),
(
  (SELECT id FROM categories WHERE slug = 'administratif'),
  'demande-conge',
  'Demande de congé',
  'Formulez une demande de congé auprès de votre employeur.',
  'demande conge vacances absence travail',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"fonction","label":"Poste occupé","type":"text","required":true},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date de la lettre","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur le Directeur"},
    {"name":"date_debut","label":"Date de début","type":"date","required":true},
    {"name":"date_fin","label":"Date de fin","type":"date","required":true},
    {"name":"motif","label":"Motif (facultatif)","type":"textarea","required":false}
  ]',
  'Demande de congé',
  'Objet : Demande de congé\n\n{{destinataire}},\n\nOccupant le poste de {{fonction}}, je sollicite un congé du {{date_debut}} au {{date_fin}}.\n\n{{motif}}\n\nJe vous remercie par avance de votre compréhension et reste à votre disposition pour organiser la passation nécessaire.\n\nVeuillez agréer, {{destinataire}}, mes salutations distinguées.'
),
(
  (SELECT id FROM categories WHERE slug = 'administratif'),
  'lettre-demission',
  'Lettre de démission',
  'Notifiez officiellement votre départ de l’entreprise.',
  'lettre demission depart travail emploi',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"fonction","label":"Poste occupé","type":"text","required":true},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date de la lettre","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur le Directeur"},
    {"name":"entreprise","label":"Entreprise","type":"text","required":true},
    {"name":"date_depart","label":"Date de fin de préavis souhaitée","type":"date","required":true},
    {"name":"message","label":"Message complémentaire (facultatif)","type":"textarea","required":false}
  ]',
  'Lettre de démission',
  'Objet : Démission\n\n{{destinataire}},\n\nPar la présente, je vous informe de ma décision de démissionner de mon poste de {{fonction}} au sein de {{entreprise}}, à compter du {{date_depart}}, sous réserve du respect du préavis convenu.\n\n{{message}}\n\nJe vous remercie de la confiance que vous m’avez accordée durant cette collaboration.\n\nVeuillez agréer, {{destinataire}}, l’expression de mes salutations distinguées.'
),
(
  (SELECT id FROM categories WHERE slug = 'administratif'),
  'demande-officielle',
  'Demande officielle',
  'Un modèle de demande formelle à usage administratif général.',
  'demande officielle administration lettre formelle',
  'letter',
  '[
    {"name":"nom","label":"Votre nom complet","type":"text","required":true},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"destinataire","label":"Destinataire","type":"text","required":true,"placeholder":"Madame, Monsieur"},
    {"name":"objet","label":"Objet de la demande","type":"text","required":true},
    {"name":"contenu","label":"Contenu de la demande","type":"textarea","required":true}
  ]',
  '{{objet}}',
  'Objet : {{objet}}\n\n{{destinataire}},\n\n{{contenu}}\n\nJe vous remercie de l’attention portée à ma demande et reste à votre disposition pour tout complément d’information.\n\nVeuillez agréer, {{destinataire}}, l’expression de mes salutations distinguées.'
);

-- ---------- Entreprise ----------

INSERT INTO documents (category_id, slug, name, description, keywords, pdf_view, fields_schema, subject_template, body_template) VALUES
(
  (SELECT id FROM categories WHERE slug = 'entreprise'),
  'facture',
  'Facture',
  'Générez une facture claire pour vos clients.',
  'facture vente client paiement entreprise',
  'invoice',
  '[
    {"name":"numero","label":"Numéro de facture","type":"text","required":true,"placeholder":"FAC-2026-001"},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"vendeur_nom","label":"Nom de l’entreprise (vendeur)","type":"text","required":true},
    {"name":"vendeur_adresse","label":"Adresse du vendeur","type":"text","required":false},
    {"name":"client_nom","label":"Nom du client","type":"text","required":true},
    {"name":"client_adresse","label":"Adresse du client","type":"text","required":false},
    {"name":"tva","label":"Taux de TVA (%)","type":"number","required":false,"placeholder":"20"}
  ]',
  NULL,
  NULL
),
(
  (SELECT id FROM categories WHERE slug = 'entreprise'),
  'devis',
  'Devis',
  'Proposez un devis détaillé avant facturation.',
  'devis proposition prix entreprise client',
  'invoice',
  '[
    {"name":"numero","label":"Numéro de devis","type":"text","required":true,"placeholder":"DEV-2026-001"},
    {"name":"date","label":"Date","type":"date","required":true},
    {"name":"vendeur_nom","label":"Nom de l’entreprise","type":"text","required":true},
    {"name":"vendeur_adresse","label":"Adresse de l’entreprise","type":"text","required":false},
    {"name":"client_nom","label":"Nom du client","type":"text","required":true},
    {"name":"client_adresse","label":"Adresse du client","type":"text","required":false},
    {"name":"tva","label":"Taux de TVA (%)","type":"number","required":false,"placeholder":"20"},
    {"name":"validite","label":"Validité du devis","type":"text","required":false,"placeholder":"30 jours"}
  ]',
  NULL,
  NULL
),
(
  (SELECT id FROM categories WHERE slug = 'entreprise'),
  'attestation',
  'Attestation',
  'Attestation de travail ou de situation, prête à signer.',
  'attestation travail certificat entreprise',
  'attestation',
  '[
    {"name":"entreprise","label":"Nom de l’entreprise","type":"text","required":true},
    {"name":"representant","label":"Nom du représentant","type":"text","required":true},
    {"name":"fonction_representant","label":"Fonction du représentant","type":"text","required":true,"placeholder":"Directeur général"},
    {"name":"beneficiaire","label":"Nom du bénéficiaire","type":"text","required":true},
    {"name":"objet","label":"Objet de l’attestation","type":"text","required":true,"placeholder":"Attestation de travail"},
    {"name":"contenu","label":"Détails à attester","type":"textarea","required":true},
    {"name":"ville","label":"Ville","type":"text","required":true},
    {"name":"date","label":"Date","type":"date","required":true}
  ]',
  '{{objet}}',
  'Je soussigné(e) {{representant}}, {{fonction_representant}} de {{entreprise}}, atteste que :\n\n{{beneficiaire}}\n\n{{contenu}}\n\nLa présente attestation est délivrée à l’intéressé(e) pour servir et valoir ce que de droit.'
);
