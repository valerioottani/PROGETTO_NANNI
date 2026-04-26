-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2026 at 02:38 PM
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
(2, 'attivo', '2026-04-26', '2026-12-31', 'dimagrire', 'principiante');

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
(1, 'Yoga', '', 'principiante', 60, 15, 'attivo');

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

-- --------------------------------------------------------

--
-- Table structure for table `persona`
--

CREATE TABLE `persona` (
  `id_persona` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `cognome` varchar(50) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `persona`
--

INSERT INTO `persona` (`id_persona`, `username`, `password`, `email`, `nome`, `cognome`, `telefono`) VALUES
(1, 'admin', '$2y$10$/Dtqts9TSTtUwwPO9INSEeam5DFWiJiTHTIJw/4Vn.cKJ4CA0siSe', 'admin@palestra.it', 'Mario', 'Rossi', '3331234567'),
(2, 'luca', '$2y$10$AuCLN8lSr9YDWTG1yA28weMFRtogc6WjnpZxiZf4IGMfQJrhF9FLS', 'luca@prova.it', 'Luca', 'Bianchi', '3331234567');

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
(1, 'Sala A', 'Pesi', 20, 'disponibile', NULL);

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
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_persona`);

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
  MODIFY `id_abbonamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `corso`
--
ALTER TABLE `corso`
  MODIFY `id_corso` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `iscrizione`
--
ALTER TABLE `iscrizione`
  MODIFY `id_iscrizione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lezione`
--
ALTER TABLE `lezione`
  MODIFY `id_lezione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pagamento`
--
ALTER TABLE `pagamento`
  MODIFY `id_pagamento` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `persona`
--
ALTER TABLE `persona`
  MODIFY `id_persona` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `prenotazione`
--
ALTER TABLE `prenotazione`
  MODIFY `id_prenotazione` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sala`
--
ALTER TABLE `sala`
  MODIFY `id_sala` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
