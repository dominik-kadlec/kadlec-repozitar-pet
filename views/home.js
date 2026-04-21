export default async function renderHome() {
    const response = await fetch('profile.json');
    const data = await response.json();
    
    return `
        <header style="background: white; color: #333; box-shadow: none; padding: 20px 0;">
            <h1>${data.name}</h1>
            <p class="subtitle">${data.jobTitle || 'IT Profil'}</p>
        </header>
    `;
}