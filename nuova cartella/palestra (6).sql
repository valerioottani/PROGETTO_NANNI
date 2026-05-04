-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2026 at 09:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `palestra`
--

-- --------------------------------------------------------

--
-- Table structure for table `abbonamento`
--

CREATE TABLE `abbonamento` (
  `id_abbonamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `codice` varchar(30) NOT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL,
  `costo` decimal(8,2) NOT NULL,
  `tipo` enum('mensile','annuale') NOT NULL,
  `stato` enum('attivo','scaduto','sospeso','annullato') NOT NULL DEFAULT 'attivo',
  `descrizione` text DEFAULT NULL,
  `rinnovo_automatico` tinyint(1) NOT NULL DEFAULT 0,
  `bonus_mensile` varchar(100) DEFAULT NULL,
  `bonus_annuale` varchar(100) DEFAULT NULL,
  `pagamento_rateizzato` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `abbonamento`
--

INSERT INTO `abbonamento` (`id_abbonamento`, `id_cliente`, `codice`, `data_inizio`, `data_fine`, `costo`, `tipo`, `stato`, `descrizione`, `rinnovo_automatico`, `bonus_mensile`, `bonus_annuale`, `pagamento_rateizzato`) VALUES
(8, 15, 'ABB-69F863D4869EB', '2026-05-04', '2027-05-04', 400.00, 'annuale', 'attivo', '', 0, NULL, 'Borsone', 0),
(9, 16, 'ABB-69F864748CD30', '2026-05-04', '2026-06-04', 50.00, 'mensile', 'attivo', '', 0, 'borraccia', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `attrezzatura`
--

CREATE TABLE `attrezzatura` (
  `id_attrezzatura` int(11) NOT NULL,
  `cod_inventario` varchar(30) NOT NULL,
  `marca` varchar(50) DEFAULT NULL,
  `modello` varchar(50) DEFAULT NULL,
  `nome` varchar(100) NOT NULL,
  `tipologia` varchar(50) DEFAULT NULL,
  `data_acquisto` date DEFAULT NULL,
  `descrizione` text DEFAULT NULL,
  `stato` enum('funzionante','in_riparazione','dismessa') NOT NULL DEFAULT 'funzionante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attrezzatura`
--

INSERT INTO `attrezzatura` (`id_attrezzatura`, `cod_inventario`, `marca`, `modello`, `nome`, `tipologia`, `data_acquisto`, `descrizione`, `stato`) VALUES
(1, 'att', 'technogym', '3424', 'tapis', '244', '0535-03-05', '', 'funzionante'),
(2, 'rrwef', 'erqrew', 'rewtt', 'tapis', 'rt', '0044-06-05', 'qr', 'funzionante'),
(4, 'ATT-001', 'Technogym', '', 'chest press', 'Pesi', '2025-05-04', '', 'funzionante'),
(5, 'ATT-002', 'Technogym', '', 'Leg Press', 'Pesi', '2025-03-02', '', 'funzionante'),
(6, 'ATT-003', 'Decathlon', '', 'Palla Medica', '', '2026-01-20', '', 'funzionante'),
(7, 'ATT-004', 'Decathlon', '', 'Elastico', '', '2026-01-20', '', 'funzionante');

-- --------------------------------------------------------

--
-- Table structure for table `cliente`
--

CREATE TABLE `cliente` (
  `id_persona` int(11) NOT NULL,
  `stato_iscrizione` enum('attivo','sospeso','scaduto') NOT NULL DEFAULT 'attivo',
  `data_iscrizione` date NOT NULL,
  `certificato_medico_scadenza` date DEFAULT NULL,
  `obiettivo` varchar(200) DEFAULT NULL,
  `livello` enum('principiante','intermedio','avanzato') NOT NULL DEFAULT 'principiante'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cliente`
--

INSERT INTO `cliente` (`id_persona`, `stato_iscrizione`, `data_iscrizione`, `certificato_medico_scadenza`, `obiettivo`, `livello`) VALUES
(15, 'attivo', '2026-05-04', '2026-05-06', 'massa', 'avanzato'),
(16, 'attivo', '2026-05-04', '2026-05-07', 'massa', 'avanzato');

-- --------------------------------------------------------

--
-- Table structure for table `contiene`
--

CREATE TABLE `contiene` (
  `id_sala` int(11) NOT NULL,
  `id_attrezzatura` int(11) NOT NULL,
  `quantita` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contiene`
--

INSERT INTO `contiene` (`id_sala`, `id_attrezzatura`, `quantita`) VALUES
(4, 4, 2),
(4, 5, 2),
(5, 6, 4),
(5, 7, 10);

-- --------------------------------------------------------

--
-- Table structure for table `corso`
--

CREATE TABLE `corso` (
  `id_corso` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text DEFAULT NULL,
  `livello` enum('principiante','intermedio','avanzato') NOT NULL,
  `durata_minuti` int(11) NOT NULL,
  `max_partecipanti` int(11) NOT NULL,
  `stato` enum('attivo','sospeso','terminato') NOT NULL DEFAULT 'attivo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `corso`
--

INSERT INTO `corso` (`id_corso`, `nome`, `descrizione`, `livello`, `durata_minuti`, `max_partecipanti`, `stato`) VALUES
(5, 'Yoga', '', 'principiante', 30, 15, 'attivo'),
(6, 'Pilates', '', 'principiante', 30, 10, 'attivo'),
(7, 'Funzionale', '', 'intermedio', 45, 15, 'attivo');

-- --------------------------------------------------------

--
-- Table structure for table `iscrizione`
--

CREATE TABLE `iscrizione` (
  `id_iscrizione` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_corso` int(11) NOT NULL,
  `data_iscrizione` date NOT NULL DEFAULT curdate(),
  `certificato_medico` tinyint(1) NOT NULL DEFAULT 0,
  `scadenza_iscrizione` date DEFAULT NULL,
  `stato` enum('attiva','sospesa','annullata') NOT NULL DEFAULT 'attiva'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `istruttore`
--

CREATE TABLE `istruttore` (
  `id_persona` int(11) NOT NULL,
  `tipo_contratto` enum('dipendente','collaboratore','partita_iva') NOT NULL,
  `stipendio` decimal(8,2) DEFAULT NULL,
  `data_assunzione` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `istruttore`
--

INSERT INTO `istruttore` (`id_persona`, `tipo_contratto`, `stipendio`, `data_assunzione`) VALUES
(17, 'dipendente', 1600.00, '2026-05-04'),
(18, 'dipendente', 1600.00, '2026-05-04');

-- --------------------------------------------------------

--
-- Table structure for table `lezione`
--

CREATE TABLE `lezione` (
  `id_lezione` int(11) NOT NULL,
  `id_corso` int(11) NOT NULL,
  `id_sala` int(11) NOT NULL,
  `data` date NOT NULL,
  `ora_inizio` time NOT NULL,
  `ora_fine` time NOT NULL,
  `tipo_lezione` varchar(50) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `stato` enum('programmata','in_corso','completata','annullata') NOT NULL DEFAULT 'programmata'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lezione`
--

INSERT INTO `lezione` (`id_lezione`, `id_corso`, `id_sala`, `data`, `ora_inizio`, `ora_fine`, `tipo_lezione`, `note`, `stato`) VALUES
(5, 7, 5, '2026-05-06', '10:00:00', '10:45:00', 'Gruppo', '', 'programmata'),
(6, 6, 5, '2026-07-05', '11:00:00', '11:30:00', 'Gruppo', '', 'programmata');

-- --------------------------------------------------------

--
-- Table structure for table `pagamento`
--

CREATE TABLE `pagamento` (
  `id_pagamento` int(11) NOT NULL,
  `id_abbonamento` int(11) NOT NULL,
  `importo` decimal(8,2) NOT NULL,
  `data_pagamento` date NOT NULL,
  `metodo_pagamento` enum('contanti','carta','bonifico','paypal') NOT NULL,
  `stato` enum('completato','in_attesa','fallito','rimborsato') NOT NULL DEFAULT 'completato',
  `data_scadenza` date DEFAULT NULL,
  `codice_transazione` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pagamento`
--

INSERT INTO `pagamento` (`id_pagamento`, `id_abbonamento`, `importo`, `data_pagamento`, `metodo_pagamento`, `stato`, `data_scadenza`, `codice_transazione`) VALUES
(2, 9, 50.00, '2026-05-04', 'contanti', 'completato', NULL, 'TR-00001'),
(3, 8, 400.00, '2026-05-04', 'contanti', 'completato', NULL, 'TR-00002');

-- --------------------------------------------------------

--
-- Table structure for table `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `ruolo` enum('admin','cliente','istruttore') NOT NULL DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persona`
--

INSERT INTO `persona` (`id_persona`, `username`, `email`, `password`, `nome`, `cognome`, `telefono`, `ruolo`) VALUES
(1, 'admin', 'admin@palestra.it', '$2y$10$OZsvOr4QC/ycKHoxX6v20urH/VGTGa2ZG4Wvoa7w7l6UAqxYZSLtq', 'Mario', 'Rossi', '3331234567', 'admin'),
(15, 'Federico', 'federico.rennella@gmail.com', '$2y$10$ji42h3sQNwD3CKMvuA01ZO2Dmo7/GhzWC.SS4hwCYnIfLj8wZi5cO', 'Federico', 'Rennella', '3511995572', 'cliente'),
(16, 'Valerio', 'valerio.ottani@gmail.com', '$2y$10$l0h/Jxk.zW4SdJxO/RgLJusM/6Xj86Zhw5leBYxb5W6/1bXO3ghUO', 'Valerio', 'Ottani', '3271023620', 'cliente'),
(17, 'istr_69f864c47d1ea', 'luca.bianchi@gmail.com', '$2y$10$N/qcpabcXK8d0Bxu75REz.9idCfA.g43EcqzX2p91MacA1E6HSoFK', 'Luca', 'Bianchi', '3344578622', 'istruttore'),
(18, 'istr_69f864f2f215a', 'stefano.rosso@gmail.com', '$2y$10$PM8tRO8J3Y30UbRNFLnRA.1/ZF/N9YL3FPoiNsPTVuwivJRD3hE4m', 'Stefano', 'Rosso', '3256538799', 'istruttore');

-- --------------------------------------------------------

--
-- Table structure for table `prenotazione`
--

CREATE TABLE `prenotazione` (
  `id_prenotazione` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_lezione` int(11) NOT NULL,
  `data_prenotazione` datetime NOT NULL DEFAULT current_timestamp(),
  `stato` enum('confermata','in_attesa','annullata','completata') NOT NULL DEFAULT 'confermata',
  `presenza` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prenotazione`
--

INSERT INTO `prenotazione` (`id_prenotazione`, `id_cliente`, `id_lezione`, `data_prenotazione`, `stato`, `presenza`) VALUES
(9, 15, 5, '2026-05-04 11:51:18', 'confermata', 0),
(10, 16, 5, '2026-05-04 11:52:14', 'confermata', 0),
(11, 15, 6, '2026-05-04 12:33:43', 'confermata', 0);

-- --------------------------------------------------------

--
-- Table structure for table `sala`
--

CREATE TABLE `sala` (
  `id_sala` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `tipologia` varchar(50) DEFAULT NULL,
  `capienza_max` int(11) NOT NULL,
  `stato` enum('disponibile','in_manutenzione','chiusa') NOT NULL DEFAULT 'disponibile',
  `data_ultima_manutenzione` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sala`
--

INSERT INTO `sala` (`id_sala`, `nome`, `tipologia`, `capienza_max`, `stato`, `data_ultima_manutenzione`) VALUES
(4, 'Sala 1', 'Pesi', 50, 'disponibile', '2026-01-01'),
(5, 'Sala 2', 'Corsi', 25, 'disponibile', '2026-01-01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abbonamento`
--
ALTER TABLE `abbonamento`
  ADD PRIMARY KEY (`id_abbonamento`),
  ADD UNIQUE KEY `uq_codice` (`codice`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indexes for table `attrezzatura`
--
ALTER TABLE `attrezzatura`
  ADD PRIMARY KEY (`id_attrezzatura`),
  ADD UNIQUE KEY `uq_cod_inventario` (`cod_inventario`);

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indexes for table `contiene`
--
ALTER TABLE `contiene`
  ADD PRIMARY KEY (`id_sala`,`id_attrezzatura`),
  ADD KEY `id_attrezzatura` (`id_attrezzatura`);

--
-- Indexes for table `corso`
--
ALTER TABLE `corso`
  ADD PRIMARY KEY (`id_corso`);

--
-- Indexes for table `iscrizione`
--
ALTER TABLE `iscrizione`
  ADD PRIMARY KEY (`id_iscrizione`),
  ADD UNIQUE KEY `uq_cliente_corso` (`id_cliente`,`id_corso`),
  ADD KEY `id_corso` (`id_corso`);

--
-- Indexes for table `istruttore`
--
ALTER TABLE `istruttore`
  ADD PRIMARY KEY (`id_persona`);

--
-- Indexes for table `lezione`
--
ALTER TABLE `lezione`
  ADD PRIMARY KEY (`id_lezione`),
  ADD KEY `id_corso` (`id_corso`),
  ADD KEY `id_sala` (`id_sala`);

--
-- Indexes for table `pagamento`
--
ALTER TABLE `pagamento`
  ADD PRIMARY KEY (`id_pagamento`),
  ADD KEY `id_abbonamento` (`id_abbonamento`);

--
-- Indexes for table `persona`
--
ALTER TABLE `persona`
  ADD PRIMARY KEY (`id_persona`),
  ADD UNIQUE KEY `uq_username` (`username`),
  ADD UNIQUE KEY `uq_email` (`email`);

--
-- Indexes for table `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD PRIMARY KEY (`id_prenotazione`),
  ADD UNIQUE KEY `uq_cliente_lezione` (`id_cliente`,`id_lezione`),
  ADD KEY `id_lezione` (`id_lezione`);

--
-- Indexes for table `sala`
--
ALTER TABLE `sala`
  ADD PRIMARY KEY (`id_sala`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abbonamento`
--
ALTER TABLE `abbonamento`
  MODIFY `id_abbonamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attrezzatura`
--
ALTER TABLE `attrezzatura`
  MODIFY `id_attrezzatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `corso`
--
ALTER TABLE `corso`
  MODIFY `id_corso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `iscrizione`
--
ALTER TABLE `iscrizione`
  MODIFY `id_iscrizione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lezione`
--
ALTER TABLE `lezione`
  MODIFY `id_lezione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `prenotazione`
--
ALTER TABLE `prenotazione`
  MODIFY `id_prenotazione` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `abbonamento`
--
ALTER TABLE `abbonamento`
  ADD CONSTRAINT `abbonamento_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_persona`);

--
-- Constraints for table `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `cliente_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `contiene`
--
ALTER TABLE `contiene`
  ADD CONSTRAINT `contiene_ibfk_1` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`) ON DELETE CASCADE,
  ADD CONSTRAINT `contiene_ibfk_2` FOREIGN KEY (`id_attrezzatura`) REFERENCES `attrezzatura` (`id_attrezzatura`) ON DELETE CASCADE;

--
-- Constraints for table `iscrizione`
--
ALTER TABLE `iscrizione`
  ADD CONSTRAINT `iscrizione_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_persona`),
  ADD CONSTRAINT `iscrizione_ibfk_2` FOREIGN KEY (`id_corso`) REFERENCES `corso` (`id_corso`);

--
-- Constraints for table `istruttore`
--
ALTER TABLE `istruttore`
  ADD CONSTRAINT `istruttore_ibfk_1` FOREIGN KEY (`id_persona`) REFERENCES `persona` (`id_persona`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `lezione`
--
ALTER TABLE `lezione`
  ADD CONSTRAINT `lezione_ibfk_1` FOREIGN KEY (`id_corso`) REFERENCES `corso` (`id_corso`),
  ADD CONSTRAINT `lezione_ibfk_2` FOREIGN KEY (`id_sala`) REFERENCES `sala` (`id_sala`);

--
-- Constraints for table `pagamento`
--
ALTER TABLE `pagamento`
  ADD CONSTRAINT `pagamento_ibfk_1` FOREIGN KEY (`id_abbonamento`) REFERENCES `abbonamento` (`id_abbonamento`);

--
-- Constraints for table `prenotazione`
--
ALTER TABLE `prenotazione`
  ADD CONSTRAINT `prenotazione_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_persona`),
  ADD CONSTRAINT `prenotazione_ibfk_2` FOREIGN KEY (`id_lezione`) REFERENCES `lezione` (`id_lezione`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
