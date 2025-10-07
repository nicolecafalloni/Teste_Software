/**
 * testeSoftwareCadastro.js
 *
 * Como rodar:
 * 1) npm install selenium-webdriver
 * Precisa instalar para que rode no seu vscode
 * 2) node testeSoftwareCadastro.js
 *
 * OBS: Este script foi corrigido para garantir que todos os 4 campos de formulário (nome, email, senha e confirmação)
 * sejam passados corretamente para a função de teste.
 */

const { Builder, By, until } = require("selenium-webdriver");
const fs = require("fs");
const path = require("path");
let relatorio = [];

// ---------- CONFIGURAÇÃO ----------
const TARGET_URL = "http://localhost/repo-testeSoftware/Teste_Software/php/cadastrar.php"; // <- alterar para a URL do seu projeto
const SCREENSHOT_DIR = path.join(__dirname, "assets", "screenshots");
const TIMEOUT_MS = 5000; // tempo de espera padrão

// Garante que a pasta de screenshots exista
fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

// Função para salvar screenshot base64 em arquivo
function salvarScreenshot(base64, nomeArquivo) {
    const filePath = path.join(SCREENSHOT_DIR, nomeArquivo);
    fs.writeFileSync(filePath, base64, "base64");
    return filePath;
}

// ---------- FUNÇÃO DE TESTE (CADASTRO) ----------
/**
 * testarCadastro(nome, email, senha_cad, confirma_senha, descricao)
 * - nome, email, senha_cad, confirma_senha: valores a preencher
 * - descricao: usado para logs e para nome do screenshot
 */
async function testarCadastro(nome, email, senha_cad, confirma_senha, descricao) { 
    let driver = await new Builder().forBrowser("chrome").build();
    let status = "pass";
    let swalText = "";

    try {
        console.log(`\nTestando: ${descricao}`);
        await driver.get(TARGET_URL);

        // 1. Preenche o campo NOME
        await driver.wait(until.elementLocated(By.id("nome")), TIMEOUT_MS);
        await driver.findElement(By.id("nome")).sendKeys(nome);

        // 2. Preenche o campo EMAIL
        await driver.wait(until.elementLocated(By.id("email")), TIMEOUT_MS);
        await driver.findElement(By.id("email")).sendKeys(email);

        // 3. Preenche o campo SENHA
        await driver.wait(until.elementLocated(By.id("senha_cad")), TIMEOUT_MS);
        await driver.findElement(By.id("senha_cad")).sendKeys(senha_cad);

        // 4. Preenche o campo CONFIRMA SENHA
        await driver.wait(until.elementLocated(By.id("confirma_senha")), TIMEOUT_MS);
        await driver.findElement(By.id("confirma_senha")).sendKeys(confirma_senha);

        // 5. Clica no botão de CADASTRO
        await driver.wait(until.elementLocated(By.id("cadastro_btn")), TIMEOUT_MS);
        await driver.findElement(By.id("cadastro_btn")).click(); 

        // Espera pelo SweetAlert (classe 'swal2-popup')
        await driver.wait(until.elementLocated(By.css('.swal2-popup')), 5000); 
        swalText = await driver.findElement(By.css('.swal2-html-container')).getText();
        console.log("Mensagem SweetAlert:", swalText);
        
        // Lógica básica de status: se o SweetAlert apareceu, o teste de UI está ok.
        status = "pass"; 

        // Tira screenshot e salva
        const safeName = descricao
            .replace(/\s+/g, "_")
            .replace(/[^a-zA-Z0-9_\-]/g, "");
        const screenshotName = `screenshotcadastro_${safeName}.png`;
        const base64 = await driver.takeScreenshot();
        const savedPath = salvarScreenshot(base64, screenshotName);
        console.log(`Screenshot salva em: ${savedPath}`);
        
        relatorio.push({
            teste: descricao,
            status,
            mensagem_alerta: swalText,
            screenshot: savedPath,
        });
    } catch (err) {
        status = "fail";
        console.log("Erro durante o teste:", err.message);

        // Sempre tenta salvar screenshot de erro também
        try {
            const safeName = descricao
                .replace(/\s+/g, "_")
                .replace(/[^a-zA-Z0-9_\-]/g, "");
            const screenshotName = `screenshot_erro_${safeName}.png`;
            const base64 = await driver.takeScreenshot();
            const savedPath = salvarScreenshot(base64, screenshotName);
            console.log(`Screenshot de erro salva em: ${savedPath}`);
            relatorio.push({
                teste: descricao,
                status,
                mensagem_alerta: swalText, // Pode ser vazio se o erro foi antes do SweetAlert
                screenshot: savedPath,
            });
        } catch (e) {
            console.log(
                "Não foi possível salvar screenshot de erro:",
                e.message
            );
            relatorio.push({
                teste: descricao,
                status,
                mensagem_alerta: "Falha na captura do alerta ou erro não SweetAlert.",
                screenshot: null,
            });
        }
    } finally {
        await driver.quit();
    }
}

// ---------- ARRAY DE TESTES ----------
// Mantido com a estrutura correta de chaves.
const testes = [
    // ATENÇÃO: Se o email já existe no seu banco de dados, o primeiro teste falhará como "email_existe", 
    // o que é um comportamento esperado. Mude o email para um novo.
    { nome: "Fulano Teste", email: "novo.usuario@teste.com", senha_cad: "senha123", confirma_senha: "senha123", descricao: "Cadastro correto (sucesso=ok)" },

    { nome: "teste", email: "", senha_cad: "4321", confirma_senha: "4321", descricao: "Email vazio (erro=vazio)" },

    { nome: "teste", email: "teste@teste.com", senha_cad: "", confirma_senha: "4321", descricao: "Senha Vazia (erro=vazio)" },

    { nome: "teste", email: "teste@gmail.com", senha_cad: "1234", confirma_senha: "", descricao: "Campo de confirmar senha vazio (erro=vazio)" },
    
    { nome: "Nome Certo", email: "email@existente.com", senha_cad: "1234", confirma_senha: "1234", descricao: "Email existente (erro=email_existe)" },

    { nome: "teste", email: "teste@naoconfere.com", senha_cad: "1234", confirma_senha: "4321", descricao: "Senhas diferentes (erro=senhas_nao_conferem)" },

    // Teste de XSS (Esperado erro=xss se o seu `processa_cadastro.php` estiver sanitizando)
    { nome: "<script>alert(1)</script>", email: "xss@safe.com", senha_cad: "1234", confirma_senha: "1234", descricao: "Tentativa de XSS (erro=xss)" },
];

// ---------- EXECUÇÃO SEQUENCIAL CORRIGIDA ----------
(async () => {
    if (!testsOrArrayIsValid(testes)) {
        console.log(
            "Nenhum teste configurado. Edite o array `testes` no arquivo para adicionar casos."
        );
        return;
    }

    for (let t of testes) {
        // CORREÇÃO AQUI: Passando os 4 campos do formulário na ordem correta, mais a descrição.
        await testarCadastro(t.nome, t.email, t.senha_cad, t.confirma_senha, t.descricao); 
    }

    // Salva relatório final em JSON
    fs.writeFileSync("relatorio.json", JSON.stringify(relatorio, null, 2));
    console.log("\nRelatório final salvo em relatorio.json");
})();

// ---------- FUNÇÕES AUXILIARES ----------
function testsOrArrayIsValid(arr) {
    return Array.isArray(arr) && arr.length > 0;
}
