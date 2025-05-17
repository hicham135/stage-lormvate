document.addEventListener('DOMContentLoaded', function() {
    // Toggle du menu latéral
    const menuToggle = document.getElementById('menu-toggle');
    if (menuToggle) {
        menuToggle.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('wrapper').classList.toggle('toggled');
        });
    }

    // Animation pour les cartes du tableau de bord
    const dashboardCards = document.querySelectorAll('.card');
    dashboardCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 0.5rem 1rem rgba(0, 0, 0, 0.15)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
        });
    });

    // Fermeture automatique des alertes après 5 secondes
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            const closeButton = alert.querySelector('.btn-close');
            if (closeButton) {
                closeButton.click();
            }
        }, 5000);
    });

    // Recherche dans les tableaux
    const searchInputs = document.querySelectorAll('input[id$="-search"]');
    searchInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const tableId = this.id.replace('-search', '');
            const table = document.querySelector(`table#${tableId}-table, table`);
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                const searchValue = this.value.toLowerCase();
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    if (text.includes(searchValue)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }
        });
    });

    // Initialiser les tooltips Bootstrap
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Modifier l'affichage des dates
    const datePickers = document.querySelectorAll('input[type="date"]');
    datePickers.forEach(picker => {
        picker.addEventListener('change', function() {
            const dateObj = new Date(this.value);
            if (!isNaN(dateObj.getTime())) {
                const formattedDate = dateObj.toLocaleDateString('fr-FR');
                this.setAttribute('data-formatted-date', formattedDate);
            }
        });
    });
});