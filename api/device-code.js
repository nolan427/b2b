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

    const CLIENT_ID = 'd2301a85-917a-484c-8c6b-d3273eebd8dd';
    const TENANT_ID = 'common';
    const SCOPE = 'openid profile email User.Read offline_access';

    const { action, device_code } = req.body;

    try {
        if (action === 'request') {
            // Request device code from Microsoft
            const url = `https://login.microsoftonline.com/${TENANT_ID}/oauth2/v2.0/devicecode`;
            
            const params = new URLSearchParams({
                client_id: CLIENT_ID,
                scope: SCOPE
            });

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Microsoft API error: ${response.status} - ${errorText}`);
            }

            const data = await response.json();
            
            return res.status(200).json({
                success: true,
                data: data
            });

        } else if (action === 'poll') {
            // Poll for token
            if (!device_code) {
                return res.status(400).json({
                    success: false,
                    error: 'device_code is required'
                });
            }

            const url = `https://login.microsoftonline.com/${TENANT_ID}/oauth2/v2.0/token`;
            
            const params = new URLSearchParams({
                client_id: CLIENT_ID,
                grant_type: 'urn:ietf:params:oauth:grant-type:device_code',
                device_code: device_code
            });

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: params.toString()
            });

            const data = await response.json();
            
            return res.status(200).json({
                success: true,
                http_code: response.status,
                data: data
            });

        } else {
            return res.status(400).json({
                success: false,
                error: 'Invalid action. Use "request" or "poll"'
            });
        }

    } catch (error) {
        console.error('Device code error:', error);
        return res.status(500).json({
            success: false,
            error: error.message
        });
    }
}