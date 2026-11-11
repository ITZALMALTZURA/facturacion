
CREATE DATABASE IF NOT EXISTS dbfacturacion
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE dbfacturacion;

-- 1. CLIENTES
CREATE TABLE clientes (
    clijid INT AUTO_INCREMENT PRIMARY KEY,
    clirfc VARCHAR(13) NOT NULL,
    clinom VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

-- 2. MONEDAS
CREATE TABLE monedas (
    monid INT AUTO_INCREMENT PRIMARY KEY,
    moncve VARCHAR(3) NOT NULL UNIQUE,
    monnom VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

-- 3. SERIES
CREATE TABLE series (
    serid INT AUTO_INCREMENT PRIMARY KEY,
    sertpo VARCHAR(10) NOT NULL,
    sernom VARCHAR(20) NOT NULL
) ENGINE=InnoDB;

-- 4. FACTURAS
CREATE TABLE facturas (
    serid INT NOT NULL,
    facid INT NOT NULL,
    clijid INT NOT NULL,
    monid INT NOT NULL,
    factc DECIMAL(12,4) NOT NULL DEFAULT 1,
    facfec DATE NOT NULL,
    facsub DECIMAL(12,2) NOT NULL DEFAULT 0,
    faciva DECIMAL(12,2) NOT NULL DEFAULT 0,
    factot DECIMAL(12,2) NOT NULL DEFAULT 0,

    PRIMARY KEY (serid, facid),

    FOREIGN KEY (serid) REFERENCES series(serid),
    FOREIGN KEY (clijid) REFERENCES clientes(clijid),
    FOREIGN KEY (monid) REFERENCES monedas(monid)
) ENGINE=InnoDB;

-- 5. FACTURAS DETALLE
CREATE TABLE facturas_detalle (
    serid INT NOT NULL,
    facid INT NOT NULL,
    facdetid INT NOT NULL,
    facdetcan DECIMAL(12,2) NOT NULL,
    facdetval DECIMAL(12,2) NOT NULL,
    facdetcon VARCHAR(255) NOT NULL,

    PRIMARY KEY (serid, facid, facdetid),

    FOREIGN KEY (serid, facid)
        REFERENCES facturas(serid, facid)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- DATOS INICIALES

INSERT INTO clientes (clirfc, clinom) VALUES
('VAGH960101ABC', 'Hector Vazquez'),
('XAXX010101000', 'Publico en General'),
('GOMJ850505XYZ', 'Juan Gomez');

INSERT INTO monedas (moncve, monnom) VALUES
('MXN', 'Pesos Mexicanos'),
('USD', 'Dolares Americanos');

INSERT INTO series (sertpo, sernom) VALUES
('I', 'Factura');
