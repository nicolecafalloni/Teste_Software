/**
 * testeAutomatizado.js
 *
 * Como rodar:
 * 1) npm install selenium-webdriver
 * Precisa instalar para que rode no seu vscode
 * O comando abaixo só será rodado quando tiver definido todas os teste de software
 * 2) node testeAutomatizado.js
 *
 * OBS: este é um template "vazio", você precisa preencher conforme for achando necessário.
 */

const { Builder, By, until } = require("selenium-webdriver");
const fs = require("fs");
const path = require("path");
let relatorio = [];

// ---------- CONFIGURAÇÃO ----------
const TARGET_URL = "http://localhost/repo-testeSoftware/Teste_Software/"; // <- alterar para a URL do seu projeto
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

// ---------- FUNÇÃO DE TESTE (padrão) ----------
/**
 * testarLogin(email, senha, descricao)
 * - email, senha: valores a preencher (pode ser string vazia)
 * - descricao: usado para logs e para nome do screenshot
 *
 * O corpo já contém as esperas básicas por elementos com ids:
 *   - #email
 *   - #senha
 *   - #btn-login
 *   - #mensagem
 *
 * Alunos devem manter esses ids ou ajustar os seletores conforme a sua página.
 */
async function testarLogin(email, senha, descricao) {
	let driver = await new Builder().forBrowser("chrome").build();
	let status = "pass";

	try {
		console.log(`\nTestando: ${descricao}`);
		await driver.get(TARGET_URL);

		// Espera pelo campo email e preenche
		await driver.wait(until.elementLocated(By.id("email")), TIMEOUT_MS);
		await driver.findElement(By.id("email")).sendKeys(email);

		// Espera pelo campo senha e preenche
		await driver.wait(until.elementLocated(By.id("senha")), TIMEOUT_MS);
		await driver.findElement(By.id("senha")).sendKeys(senha);

		// Clica no botão de login
		await driver.wait(until.elementLocated(By.id("btn-login")), TIMEOUT_MS);
		await driver.findElement(By.id("btn-login")).click();

		// Aguarda a div de mensagem (pode conter erro ou sucesso)
		// Se o projeto de vocês redireciona após login, você deve adaptar para verificar URL ou um elemento da página alvo específica.

		// await driver.wait(until.elementLocated(By.id("mensagem")), TIMEOUT_MS);
		// const mensagem = await driver.findElement(By.id("mensagem")).getText();
		// console.log("Mensagem recebida:", mensagem);

		// Tira screenshot e salva
		const safeName = descricao
			.replace(/\s+/g, "_")
			.replace(/[^a-zA-Z0-9_\-]/g, "");
		const screenshotName = `screenshot_${safeName}.png`;
		const base64 = await driver.takeScreenshot();
		const savedPath = salvarScreenshot(base64, screenshotName);
		console.log(`Screenshot salva em: ${savedPath}`);
		relatorio.push({
			teste: descricao,
			status,
			// mensagem,
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
				mensagem,
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
				mensagem,
				screenshot: null,
			});
		}
	} finally {
		await driver.quit();
	}
}

// ---------- ARRAY DE TESTES (VAZIO POR PADRÃO) ----------
// VocÊs devem preencher esse array com os casos de teste que desejarem porém seguindo o padrão que definiu no BD e tudo mais.
// Exemplo (COMENTADO):
/*
const testes = [
  { email: "admin@teste.com", senha: "1234", descricao: "Login correto" },
  { email: "admin@teste.com", senha: "errada", descricao: "Senha incorreta" },
  { email: "", senha: "1234", descricao: "Campo email vazio" },
  { email: "admin@teste.com", senha: "", descricao: "Campo senha vazio" },
  { email: "<script>", senha: "1234", descricao: "Tentativa de XSS" },
];
*/

// Padrão vazio para os vocês preencherem:
const testes = [
	{ email: "teste@gmail.com", senha: "1234", descricao: "Login correto" },
	{ email: "teste@gmail.com", senha: "4321", descricao: "Senha incorreta" },
    { email: "teste@teste.com", senha: "4321", descricao: "Email incorreta" },
	{ email: "", senha: "1234", descricao: "Campo email vazio" },
	{ email: "teste@gmail.com", senha: "", descricao: "Campo senha vazio" },
	{ email: "<script>", senha: "1234", descricao: "Tentativa de XSS" },
    { email: "' OR '1'='1", senha: "1234", descricao: "Tentativa de SQL Injection" },
];

// ---------- EXECUÇÃO SEQUENCIAL ----------
(async () => {
	if (!testsOrArrayIsValid(testes)) {
		console.log(
			"Nenhum teste configurado. Edite o array `testes` no arquivo para adicionar casos."
		);
		return;
	}

	for (let t of testes) {
		await testarLogin(t.email, t.senha, t.descricao);
	}

	// Salva relatório final em JSON
	fs.writeFileSync("relatorio.json", JSON.stringify(relatorio, null, 2));
	console.log("\nRelatório final salvo em relatorio.json");
})();

// ---------- FUNÇÕES AUXILIARES ----------
function testsOrArrayIsValid(arr) {
	return Array.isArray(arr) && arr.length > 0;
}