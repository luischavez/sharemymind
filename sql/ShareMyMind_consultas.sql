---------------- Usuarios ----------------
-- Obtener el usuario a partir de su id.
SELECT *
FROM users
WHERE user_id = ?

-- Obtener el usuario a partir de su nombre de usuario.
SELECT *
FROM users
WHERE user_name = ?

-- Obtener el usuario a partir de su nombre de su email.
SELECT *
FROM users
WHERE email = ?

-- Actualizar el token de sesión del usuario.
UPDATE users
SET token = ?
WHERE user_id = ?

-- Crea un nuevo usuario.
INSERT INTO users
	(first_name, last_name, birthdate, email, user_name, password)
VALUES (?, ?, ?, ?, ?, ?, ?)

-- Actualiza un usuario.
UPDATE users
SET
	first_name = ?,
	last_name = ?,
	birthdate = ?,
	email = ?,
	password = ?
WHERE user_id = ?

-- Actualiza el color de los shares del usuario.
UPDATE users
SET
	share_color = ?
WHERE user_id = ?
---------------- Shares ----------------
-- Obtener el TOP de shares.
SELECT share_id
FROM likes
GROUP BY share_id
ORDER BY COUNT(share_id) DESC
LIMIT ?

-- Obtener un share.
SELECT *
FROM shares
WHERE share_id = ?

-- Obtener los ultimos shares.
SELECT *
FROM shares
ORDER BY created_at DESC
LIMIT ?

-- Obtener los shares paginados del usuario.
SELECT *
FROM shares
WHERE user_id = ?
ORDER BY created_at DESC
LIMIT ?, ?

-- Obtener el numero de shares del usuario.
SELECT COUNT(*) share_count
FROM shares
WHERE user_id = ?

-- Crea un nuevo share de parte de un usuario.
INSERT INTO shares
	(user_id, text, created_at)
VALUES (?, ?, ?)

-- Elimina un share.
DELETE
FROM shares
WHERE share_id = ?
---------------- Likes ----------------
-- Obtener el numero de likes de un share.
SELECT COUNT(share_id) AS likes
FROM likes
WHERE share_id = ?
GROUP BY share_id

-- Verifica si un usuario le dio like a un share.
SELECT 1 AS liked
FROM likes
WHERE share_id = ?
	AND user_id = ?

-- Registra un nuevo like a un share de parte de un usuario.
INSERT INTO likes
	(share_id, user_id)
VALUES (?, ?)

-- Elimina un like de un share de parte de un usuario.
DELETE
FROM likes
WHERE share_id = ?
	AND user_id = ?