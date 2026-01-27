export default async function handler(req, res) {
  try {
    const r = await fetch("https://checkpersonal.com/api/generate-key", {
      headers: {
        "User-Agent": "vercel-key-proxy/1.0",
        "Accept": "application/json,text/plain,*/*"
      }
    });

    if (!r.ok) {
      return res.status(502).json({ ok: false, status: r.status });
    }

    const text = await r.text();

    // Try JSON first; fall back to plain text
    let key;
    try {
      const json = JSON.parse(text);
      key = json.key || json.api_key || json.token || json.result;
      if (!key) key = text;
    } catch {
      key = text;
    }

    key = String(key).trim();

    // Send key back to your page
    return res.status(200).json({ ok: true, key });
  } catch (e) {
    return res.status(500).json({ ok: false, error: "proxy_failed" });
  }
}
