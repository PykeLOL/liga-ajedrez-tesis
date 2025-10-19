-- USUARIOS, ROLES Y PERMISOS
CREATE TABLE roles (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    documento TEXT,
    email TEXT UNIQUE NOT NULL,
    telefono TEXT,
    contraseña TEXT NOT NULL,
    imagen_path TEXT,
    estado BOOLEAN DEFAULT TRUE,
    rol_id INTEGER,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_roles FOREIGN KEY (rol_id)
        REFERENCES roles (id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);

CREATE TABLE permisos (
    id SERIAL PRIMARY KEY,
    nombre TEXT NOT NULL,
    descripcion TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE usuarios_permisos (
    id SERIAL PRIMARY KEY,
    usuario_id INTEGER NOT NULL,
    permiso_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuarios_permisos_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_usuarios_permisos_permiso FOREIGN KEY (permiso_id)
        REFERENCES permisos (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT unq_usuario_permiso UNIQUE (usuario_id, permiso_id)
);

CREATE TABLE roles_permisos (
    id SERIAL PRIMARY KEY,
    rol_id INTEGER NOT NULL,
    permiso_id INTEGER NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_roles_permisos_rol FOREIGN KEY (rol_id)
        REFERENCES roles (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT fk_roles_permisos_permiso FOREIGN KEY (permiso_id)
        REFERENCES permisos (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,
    CONSTRAINT unq_rol_permiso UNIQUE (rol_id, permiso_id)
);


-- Actualizacion 15/10/2025
ALTER TABLE usuarios ADD COLUMN apellido TEXT NOT NULL DEFAULT '';
