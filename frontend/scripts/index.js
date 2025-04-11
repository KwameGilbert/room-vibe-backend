document.addEventListener("DOMContentLoaded", function () {

    const pageContent = document.getElementById("page-content");
    const navLinks = document.querySelectorAll(".nav-link");
    const activeColor = "#fd7e14";
    const inactiveColor = "gray";
    
    // Function to execute scripts in loaded content
    function executeScripts(element) {
        const scripts = element.querySelectorAll("script");
        scripts.forEach(oldScript => {
            const newScript = document.createElement("script");
            Array.from(oldScript.attributes).forEach(attr => {
                newScript.setAttribute(attr.name, attr.value);
            });
            newScript.textContent = oldScript.textContent;
            oldScript.parentNode.replaceChild(newScript, oldScript);
        });
    }
    
    // Function to update active navigation state
    function updateNavState(page) {
        // Reset all navigation items to inactive state
        navLinks.forEach(link => {
            const icon = link.querySelector("i");
            const text = link.querySelector("p");
            if (icon) icon.style.color = inactiveColor;
            if (text) text.style.color = inactiveColor;
        });
        
        // Set active state for selected page
        const activeItem = document.querySelector(`.nav-link.${page}`);
        if (activeItem) {
            const icon = activeItem.querySelector("i");
            const text = activeItem.querySelector("p");
            if (icon) icon.style.color = activeColor;
            if (text) text.style.color = activeColor;
        }
    }
    
    // Function to display loading indicator
    function showLoading() {
        return '<div class="text-center text-orange-400 py-4 h-screen flex items-center justify-center"><i class="fas fa-spinner fa-spin fa-3x"></i><p class="ml-3">Loading...</p></div>';
    }
    
    // Function to display error message
    function showError() {
        return `
            <div class="text-center py-4 mt-36">
                <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-3"></i>
                <p class="text-gray-700">An error occurred while loading the page. Please try again.</p>
                <button class="mt-3 px-4 py-2 bg-orange-500 text-white rounded" 
                        onclick="loadPage('explore')">Return to Explore</button>
            </div>
        `;
    }
    
    // Function to load page content
    function loadPage(page) {
        // Save current page to localStorage
        localStorage.setItem("currentPage", page);
        
        // Update navigation visual state
        updateNavState(page);
        
        // Show loading indicator
        pageContent.innerHTML = showLoading();
        
        // Set loading timer
        const loadingStartTime = Date.now();
        const minLoadingTime = 500; // Minimum loading time in milliseconds
        
        // Fetch page content
        fetch(`./${page}/index.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(data => {
                // Calculate remaining display time for loader
                const fetchTime = Date.now() - loadingStartTime;
                const remainingTime = Math.max(0, minLoadingTime - fetchTime);
                
                // Update content after minimum loading time
                setTimeout(() => {
                    pageContent.innerHTML = data;
                    executeScripts(pageContent);
                    // Ensure nav state is correct (in case it was reset during loading)
                    
                    if(page != "explore"){updateNavState(page);}
                }, remainingTime);
            })
            .catch(error => {
                console.error("Error loading page:", error);
                
                // Show error message after minimum loading time
                const fetchTime = Date.now() - loadingStartTime;
                const remainingTime = Math.max(0, minLoadingTime - fetchTime);
                
                setTimeout(() => {
                    pageContent.innerHTML = showError();
                }, remainingTime);
            });
    }
    
    // Initialize: Load saved page or default to explore
    const savedPage = localStorage.getItem("currentPage") || "explore";
    loadPage(savedPage);
    
    // Attach click event listeners to navigation items
    navLinks.forEach(link => {
        link.addEventListener("click", function() {
            const page = this.getAttribute("data-page");
            if (page) {
                loadPage(page);
            }
        });
    });
    
    // Make loadPage function available globally
    window.loadPage = loadPage;
});