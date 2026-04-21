export default async function renderInterests() {
    const response = await fetch('profile.json');
    const data = await response.json();
    
    // Pro ukázku SPA načítáme zájmy pouze pro čtení dle zadání
    const interestsList = data.interests.map(interest => `
        <li class="interest-item" style="justify-content: flex-start;">
            <span>${interest}</span>
        </li>
    `).join('');
    
    return `
        <section>
            <h2>Zájmy</h2>
            <ul>
                ${interestsList}
            </ul>
        </section>
    `;
}