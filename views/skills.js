export default async function renderSkills() {
    const response = await fetch('profile.json');
    const data = await response.json();
    
    // Převedení pole dovedností na HTML seznam
    const skillsList = data.skills.map(skill => `<li>${skill}</li>`).join('');
    
    return `
        <section>
            <h2>Dovednosti</h2>
            <ul class="skills-list">
                ${skillsList}
            </ul>
        </section>
    `;
}