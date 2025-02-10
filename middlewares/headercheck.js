const headerMiddleware = (req, res, next) => {
    const token = req.headers['x-custom-token'];
    
    console.log('Token recibido:', token); // Para debug
    console.log('Headers:', req.headers); // Para debug
    console.log('Query:', req.query); // Para debug

    if (!token || token !== '111') {
        return res.status(401).json({ 
            error: 'Acceso no autorizado: cabecera inválida o inexistente'
        });
    }

    next();
};

module.exports = headerMiddleware;
