// Importujeme naše rozdělené soubory
import renderHome from './views/home.js';
import renderInterests from './views/interests.js';
import renderSkills from './views/skills.js';

const app = document.getElementById('app');

// Hlavní renderovací funkce routeru
async function render() {
    // Přečteme aktuální hash z URL (např. "#/home")
    let route = window.location.hash;

    // Pokud je URL prázdná, nastavíme výchozí stránku na home
    if (route === "" || route === "#/") {
        route = "#/home";
        window.location.hash = route;
    }

    // Podle hashe vykreslíme odpovídající obsah
    if (route === "#/home") {
        app.innerHTML = await renderHome();
    } else if (route === "#/interests") {
        app.innerHTML = await renderInterests();
    } else if (route === "#/skills") {
        app.innerHTML = await renderSkills();
    } else {
        app.innerHTML = `<h2>404</h2><p>Stránka nenalezena.</p>`;
    }
}

// Spustíme render při načtení stránky a při každé změně hashe v URL
window.addEventListener("load", render);
window.addEventListener("hashchange", render);