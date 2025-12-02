/**
 * Sidebar auto-scroll enhancement
 * Makes sidebar automatically scroll when expandable sections are opened
 */
document.addEventListener('DOMContentLoaded', function() {
    // Find all expandable links in the sidebar
    const sidebarLinks = document.querySelectorAll('.sidebar a[data-toggle="collapse"]');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (!targetElement) return;
            
            // Wait for collapse animation to finish before scrolling
            setTimeout(function() {
                if (link.getAttribute('aria-expanded') === 'true') {
                    // Get sidebar scrollable area
                    const sidebarWrapper = document.querySelector('.sidebar-wrapper');
                    
                    // Get position of expanded element
                    const expandedRect = targetElement.getBoundingClientRect();
                    const sidebarRect = sidebarWrapper.getBoundingClientRect();
                    
                    // Calculate scroll position (align slightly above the element)
                    let scrollTop = sidebarWrapper.scrollTop + (expandedRect.top - sidebarRect.top) - 100;
                    
                    // Smooth scroll to expanded section
                    sidebarWrapper.scrollTo({
                        top: scrollTop,
                        behavior: 'smooth'
                    });
                }
            }, 350); // Match Bootstrap's collapse transition duration
        });
    });
});