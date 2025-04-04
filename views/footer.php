<!-- footer.php -->
    </main><!-- Close main content -->
    
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="/index.php?page=about">About Us</a>
                <a href="/index.php?page=contact">Contact</a>
                <a href="/index.php?page=all_recipes">All Recipes</a>
            </div>
            <p>&copy; <?= date('Y') ?> Curfew Comforts. All rights reserved.</p>
        </div>
    </footer>

    <style>
        footer {
            background-color: #F5F7FA;
            padding: 3rem 0;
            margin-top: 3rem;
            border-top: 1px solid #E5E7EB;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            text-align: center;
        }

        .footer-links {
            margin-bottom: 1rem;
        }

        .footer-links a {
            margin: 0 1rem;
            color: #2C3E50;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-links a:hover {
            color: #E74C3C;
        }

        .footer-links a::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 2px;
            bottom: -4px;
            left: 0;
            background-color: #E74C3C;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .footer-links a:hover::after {
            transform: scaleX(1);
        }

        footer p {
            color: #7F8C8D;
            font-size: 0.9rem;
        }

        /* Dark mode */
        @media (prefers-color-scheme: dark) {
            footer {
                background-color: #2D2D2D;
                border-top-color: #404040;
            }

            .footer-links a {
                color: #ECF0F1;
            }

            .footer-links a:hover {
                color: #E74C3C;
            }

            footer p {
                color: #BDC3C7;
            }
        }
    </style>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded'); // Debug log
            
            const hamburger = document.querySelector('.hamburger');
            const navMenu = document.querySelector('.nav-menu');
            
            // Remove loading state once JS is loaded
            document.documentElement.classList.remove('no-js');
            
            console.log('Hamburger:', hamburger); // Debug log
            console.log('Nav Menu:', navMenu); // Debug log

            if (hamburger && navMenu) {
                console.log('Both elements found, adding click handlers'); // Debug log
                
                hamburger.addEventListener('click', function() {
                    console.log('Hamburger clicked'); // Debug log
                    navMenu.classList.toggle('active');
                    hamburger.classList.toggle('active');
                });

                // Close menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!hamburger.contains(event.target) && !navMenu.contains(event.target)) {
                        console.log('Clicked outside menu'); // Debug log
                        navMenu.classList.remove('active');
                        hamburger.classList.remove('active');
                    }
                });
            } else {
                console.log('Missing elements:', {
                    hamburger: !!hamburger,
                    navMenu: !!navMenu
                }); // Debug log
            }
        });
    </script>
</body>
</html>