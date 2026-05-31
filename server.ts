import express from "express";
import path from "path";
import fs from "fs";

/**
 * MOCK SERVER FOR AI STUDIO PREVIEW
 * Since this environment is Node-only, we can't run PHP.
 * This server will serve the project files as static content
 * to allow the user to see the UI layout.
 */

const app = express();
const PORT = 3000;

app.use(express.static(process.cwd()));

// Simple route to handle "index.php" as "/"
app.get("/", (req, res) => {
    let content = fs.readFileSync(path.join(process.cwd(), "index.php"), "utf-8");
    // Strip PHP tags for preview
    content = content.replace(/<\?php[\s\S]*?\?>/g, (match) => {
        if (match.includes("format_idr")) return "Rp 0";
        if (match.includes("get_setting('app_name')")) return "KiosDigital PPOB";
        return "";
    });
    res.send(`
        <!DOCTYPE html>
        <html>
        <head>
            <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/lucide/0.263.0/umd/lucide.min.js"></script>
        </head>
        <body class="bg-gray-50">
            ${content}
            <script>lucide.createIcons();</script>
            <div class="fixed top-0 left-0 right-0 bg-yellow-500 text-white text-[10px] text-center py-1 font-bold z-[70] uppercase tracking-widest">
                PREVIEW MODE: PHP Logic is disabled in this IDE. Use cPanel or Local PHP Server for full functionality.
            </div>
        </body>
        </html>
    `);
});

app.listen(PORT, "0.0.0.0", () => {
  console.log(`Server running on http://localhost:${PORT}`);
});
