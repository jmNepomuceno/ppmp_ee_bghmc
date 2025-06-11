const fs = require("fs");
const path = require("path");
const JavaScriptObfuscator = require("javascript-obfuscator");

// Folder containing your JavaScript files
const inputFolder = "./js/";
const outputFolder = "./js-obf/"; // Store obfuscated files separately

// Ensure output folder exists
if (!fs.existsSync(outputFolder)) {
    fs.mkdirSync(outputFolder, { recursive: true });
}

// Read all .js files in the input folder
fs.readdir(inputFolder, (err, files) => {
    if (err) {
        console.error("❌ Error reading directory:", err);
        return;
    }

    files.forEach((file) => {
        if (path.extname(file) === ".js") {
            const inputFilePath = path.join(inputFolder, file);
            const outputFilePath = path.join(outputFolder, file.replace(".js", "-obf.js"));

            // Read the original JS file
            fs.readFile(inputFilePath, "utf8", (err, code) => {
                if (err) {
                    console.error("❌ Error reading file:", err);
                    return;
                }

                // Obfuscate the JavaScript code
                const obfuscatedCode = JavaScriptObfuscator.obfuscate(code, {
                    compact: true,
                    controlFlowFlattening: true,
                    stringArray: true,
                    stringArrayEncoding: ["base64"],
                    stringArrayThreshold: 0.75,
                    renameGlobals: true,
                }).getObfuscatedCode();

                // Save the obfuscated file
                fs.writeFile(outputFilePath, obfuscatedCode, (err) => {
                    if (err) {
                        console.error("❌ Error writing file:", err);
                        return;
                    }
                    console.log(`✅ Obfuscated: ${file} → ${outputFilePath}`);
                });
            });
        }
    });
});
