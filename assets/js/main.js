document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
    const nav = document.querySelector('nav');
    
    if (mobileMenuBtn) {
        mobileMenuBtn.addEventListener('click', function() {
            nav.classList.toggle('active');
        });
    }
    
    // Favorite button functionality
    const favoriteButtons = document.querySelectorAll('.favorite-btn');
    
    favoriteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const recipeId = this.getAttribute('data-recipe-id');
            const icon = this.querySelector('i');
            const button = this;
            
            // Send AJAX request
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'includes/ajax-handler.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    
                    if (response.success) {
                        // Update UI based on response
                        if (response.is_favorite) {
                            icon.className = 'fas fa-heart';
                            button.classList.add('active');
                            if (button.querySelector('span')) {
                                button.querySelector('span').textContent = 'Favorited';
                            }
                        } else {
                            icon.className = 'far fa-heart';
                            button.classList.remove('active');
                            if (button.querySelector('span')) {
                                button.querySelector('span').textContent = 'Add to Favorites';
                            }
                        }
                        
                        // If we're on the favorites page, potentially remove the card
                        if (!response.is_favorite && window.location.href.includes('client-dashboard.php') && 
                            document.getElementById('favorites').style.display !== 'none') {
                            const recipeCard = button.closest('.recipe-card');
                            if (recipeCard) {
                                // Fade out and remove
                                recipeCard.style.opacity = '0';
                                setTimeout(() => {
                                    recipeCard.remove();
                                    
                                    // Check if there are any recipes left
                                    const remainingCards = document.querySelectorAll('#favorites .recipe-card');
                                    if (remainingCards.length === 0) {
                                        const recipeGrid = document.querySelector('#favorites .recipe-grid');
                                        if (recipeGrid) {
                                            recipeGrid.innerHTML = `
                                                <div style="text-align: center; padding: 3rem 0; grid-column: 1 / -1;">
                                                    <h3>No Favorites Yet</h3>
                                                    <p>You haven't added any recipes to your favorites yet.</p>
                                                    <a href="recipes.php" class="btn" style="margin-top: 1rem;">Browse Recipes</a>
                                                </div>
                                            `;
                                        }
                                    }
                                }, 300);
                            }
                        }
                    }
                }
            };
            
            xhr.send('action=toggle_favorite&recipe_id=' + recipeId);
        });
    });
    
    // Dashboard tab functionality
    const dashboardTabs = document.querySelectorAll('.dashboard-tab');
    const tabButtons = document.querySelectorAll('[onclick^="showTab"]');
    
    if (dashboardTabs.length > 0 && tabButtons.length > 0) {
        // Function to show tab
        window.showTab = function(tabId) {
            // Hide all tabs
            dashboardTabs.forEach(tab => {
                tab.style.display = 'none';
            });
            
            // Show selected tab
            const selectedTab = document.getElementById(tabId);
            if (selectedTab) {
                selectedTab.style.display = 'block';
            }
            
            // Update button styles
            tabButtons.forEach(button => {
                if (button.getAttribute('href') === '#' + tabId) {
                    button.className = 'btn';
                } else {
                    button.className = 'btn btn-outline';
                }
            });
        };
    }
    
    // Category filter functionality
    const categoryButtons = document.querySelectorAll('.category-filter');
    
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Update active button
            categoryButtons.forEach(btn => {
                btn.classList.remove('active');
                btn.classList.add('btn-outline');
            });
            this.classList.add('active');
            this.classList.remove('btn-outline');
            
            // Filter recipes
            const recipes = document.querySelectorAll('.recipe-card');
            
            if (category === 'all') {
                recipes.forEach(recipe => {
                    recipe.style.display = 'block';
                });
            } else {
                recipes.forEach(recipe => {
                    if (recipe.classList.contains('category-' + category)) {
                        recipe.style.display = 'block';
                    } else {
                        recipe.style.display = 'none';
                    }
                });
            }
        });
    });
});