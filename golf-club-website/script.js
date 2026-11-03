document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;
    let scrollTimeout;

    window.addEventListener('scroll', function() {
        clearTimeout(scrollTimeout);

        // Get current scroll position
        const currentScroll = window.pageYOffset;

        // Add scrolled class for background color
        if (currentScroll > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }

        // Handle navbar hide/show
        if (currentScroll > lastScroll && currentScroll > 150) {
            // Scrolling down & past threshold - hide navbar
            navbar.classList.add('navbar-hidden');
        } else {
            // Scrolling up - show navbar
            navbar.classList.remove('navbar-hidden');
        }

        // Update last scroll position
        lastScroll = currentScroll;

        // Add small delay to prevent navbar flickering
        scrollTimeout = setTimeout(() => {
            lastScroll = currentScroll;
        }, 66);
    });
});

// Update fungsi openMenu dan closeMenu
function openMenu() {
    const menuOverlay = document.getElementById('menuOverlay');
    menuOverlay.style.display = 'block';
    // Use requestAnimationFrame to ensure display:block is applied before adding active class
    requestAnimationFrame(() => {
        menuOverlay.classList.add('active');
    });
}

function closeMenu() {
    const menuOverlay = document.getElementById('menuOverlay');
    menuOverlay.classList.remove('active');
    
    // Tunggu animasi selesai sebelum menyembunyikan overlay
    menuOverlay.addEventListener('transitionend', function handler() {
        menuOverlay.style.display = 'none';
        menuOverlay.removeEventListener('transitionend', handler);
    }, { once: true });
}

// Add click event to menu toggle button
document.querySelector('.menu-toggle').addEventListener('click', openMenu);

// Add event listener for ESC key to close menu
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMenu();
    }
});

// Weather FAB functionality using Open-Meteo API
const weatherFab = document.getElementById('weatherFab');
const weatherIcon = weatherFab.querySelector('i');
const temperature = weatherFab.querySelector('.temperature');
const condition = weatherFab.querySelector('.condition');

const CITY = 'Bogor';

async function getLocation(query) {
    try {
        const response = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(query)}&count=1&language=en&format=json`);
        const data = await response.json();
        return data.results?.[0];
    } catch (error) {
        console.error('Error fetching location:', error);
        return null;
    }
}

// Add popup HTML to weatherFab
weatherFab.insertAdjacentHTML('afterend', `
    <div class="weather-popup" id="weatherPopup">
        <div class="header">
            <h3>Weather Forecast</h3>
            <button class="close-popup">&times;</button>
        </div>
        <div class="forecast-container">
            <div class="forecast-item">
                <div class="day">Today</div>
                <i class="fas fa-sun"></i>
                <div class="temp">--°C</div>
                <div class="desc">--</div>
            </div>
            <div class="forecast-item">
                <div class="day">Tomorrow</div>
                <i class="fas fa-sun"></i>
                <div class="temp">--°C</div>
                <div class="desc">--</div>
            </div>
        </div>
    </div>
`);

const weatherPopup = document.getElementById('weatherPopup');

async function updateWeather() {
    try {
        const location = await getLocation(CITY);
        if (!location) throw new Error('Location not found');

        const weatherResponse = await fetch(
            `https://api.open-meteo.com/v1/forecast?latitude=${location.latitude}&longitude=${location.longitude}&current=temperature_2m,weather_code&daily=temperature_2m_max,temperature_2m_min,weather_code&timezone=auto`
        );
        const weatherData = await weatherResponse.json();
        
        // Update current weather
        temperature.textContent = `${Math.round(weatherData.current.temperature_2m)}°C`;
        const currentWeatherInfo = getWeatherInfo(weatherData.current.weather_code);
        condition.textContent = currentWeatherInfo.description;
        weatherIcon.className = currentWeatherInfo.icon;

        // Update forecast
        const forecastItems = weatherPopup.querySelectorAll('.forecast-item');
        
        // Today
        const todayWeatherInfo = getWeatherInfo(weatherData.daily.weather_code[0]);
        forecastItems[0].querySelector('i').className = todayWeatherInfo.icon;
        forecastItems[0].querySelector('.temp').textContent = 
            `${Math.round(weatherData.daily.temperature_2m_min[0])}° / ${Math.round(weatherData.daily.temperature_2m_max[0])}°`;
        forecastItems[0].querySelector('.desc').textContent = todayWeatherInfo.description;

        // Tomorrow
        const tomorrowWeatherInfo = getWeatherInfo(weatherData.daily.weather_code[1]);
        forecastItems[1].querySelector('i').className = tomorrowWeatherInfo.icon;
        forecastItems[1].querySelector('.temp').textContent = 
            `${Math.round(weatherData.daily.temperature_2m_min[1])}° / ${Math.round(weatherData.daily.temperature_2m_max[1])}°`;
        forecastItems[1].querySelector('.desc').textContent = tomorrowWeatherInfo.description;
        
    } catch (error) {
        console.error('Error fetching weather:', error);
        // Fallback display untuk current weather dan forecast
        setErrorState();
    }
}

function setErrorState() {
    temperature.textContent = '--°C';
    condition.textContent = 'Unavailable';
    weatherIcon.className = 'fas fa-cloud';
    
    const forecastItems = weatherPopup.querySelectorAll('.forecast-item');
    forecastItems.forEach(item => {
        item.querySelector('i').className = 'fas fa-cloud';
        item.querySelector('.temp').textContent = '--°C';
        item.querySelector('.desc').textContent = 'Unavailable';
    });
}

function getWeatherInfo(code) {
    const weatherMap = {
        0: { description: 'Clear', icon: 'fas fa-sun' },
        1: { description: 'Mostly Clear', icon: 'fas fa-sun' },
        2: { description: 'Partly Cloudy', icon: 'fas fa-cloud-sun' },
        3: { description: 'Cloudy', icon: 'fas fa-cloud' },
        45: { description: 'Foggy', icon: 'fas fa-smog' },
        48: { description: 'Foggy', icon: 'fas fa-smog' },
        51: { description: 'Light Drizzle', icon: 'fas fa-cloud-rain' },
        53: { description: 'Drizzle', icon: 'fas fa-cloud-rain' },
        55: { description: 'Heavy Drizzle', icon: 'fas fa-cloud-rain' },
        61: { description: 'Light Rain', icon: 'fas fa-cloud-rain' },
        63: { description: 'Rain', icon: 'fas fa-cloud-showers-heavy' },
        65: { description: 'Heavy Rain', icon: 'fas fa-cloud-showers-heavy' },
        71: { description: 'Light Snow', icon: 'fas fa-snowflake' },
        73: { description: 'Snow', icon: 'fas fa-snowflake' },
        75: { description: 'Heavy Snow', icon: 'fas fa-snowflake' },
        77: { description: 'Snow Grains', icon: 'fas fa-snowflake' },
        80: { description: 'Light Showers', icon: 'fas fa-cloud-rain' },
        81: { description: 'Showers', icon: 'fas fa-cloud-showers-heavy' },
        82: { description: 'Heavy Showers', icon: 'fas fa-cloud-showers-heavy' },
        95: { description: 'Thunderstorm', icon: 'fas fa-bolt' },
        96: { description: 'Thunderstorm & Hail', icon: 'fas fa-bolt' },
        99: { description: 'Thunderstorm & Heavy Hail', icon: 'fas fa-bolt' }
    };
    
    return weatherMap[code] || { description: 'Unknown', icon: 'fas fa-cloud' };
}

// Toggle weather popup
weatherFab.addEventListener('click', () => {
    weatherPopup.classList.toggle('active');
});

// Close popup when clicking close button
weatherPopup.querySelector('.close-popup').addEventListener('click', (e) => {
    e.stopPropagation();
    weatherPopup.classList.remove('active');
});

// Close popup when clicking outside
document.addEventListener('click', (e) => {
    if (!weatherFab.contains(e.target) && !weatherPopup.contains(e.target)) {
        weatherPopup.classList.remove('active');
    }
});

// Update weather initially and every 10 minutes
updateWeather();
setInterval(updateWeather, 10 * 60 * 1000);

// View All Events and Promotions functionality
document.addEventListener('DOMContentLoaded', function() {
    // Function to initialize items (events or promotions)
    function initializeItems(containerClass, hiddenClass, maxVisible) {
        const items = document.querySelectorAll(`.${containerClass} > div`);
        
        items.forEach((item, index) => {
            if (index >= maxVisible) {
                item.classList.add(hiddenClass);
                item.style.display = 'none';
            } else {
                item.classList.remove(hiddenClass);
                item.style.display = 'block';
            }
        });
    }

    // Function to toggle items visibility
    function toggleItems(items, button) {
        const isShowing = button.classList.contains('active');
        
        items.forEach(item => {
            if (item.classList.contains('hidden-event') || item.classList.contains('hidden-promo')) {
                if (!isShowing) {
                    // Show items
                    item.style.display = 'block';
                    // Use setTimeout to ensure display:block is applied before adding show class
                    setTimeout(() => {
                        item.classList.add('show');
                    }, 10);
                } else {
                    // Hide items
                    item.classList.remove('show');
                    // Wait for transition to complete before hiding
                    item.addEventListener('transitionend', function hide() {
                        item.style.display = 'none';
                        item.removeEventListener('transitionend', hide);
                    }, { once: true });
                }
            }
        });

        button.classList.toggle('active');

        // Smooth scroll if showing items
        if (!isShowing) {
            const lastItem = Array.from(items).filter(item => 
                item.classList.contains('hidden-event') || 
                item.classList.contains('hidden-promo')
            ).pop();
            
            if (lastItem) {
                setTimeout(() => {
                    lastItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        }
    }

    // Initialize events
    const eventsGrid = document.querySelector('.events-grid');
    const viewAllEventsBtn = document.getElementById('viewAllEvents');
    
    if (eventsGrid && viewAllEventsBtn) {
        initializeItems('events-grid', 'hidden-event', 3);
        
        viewAllEventsBtn.addEventListener('click', function() {
            const events = eventsGrid.querySelectorAll('.event-card');
            toggleItems(events, this);
        });
    }

    // Initialize promotions
    const promotionsGrid = document.querySelector('.promotions-grid');
    const viewAllPromosBtn = document.getElementById('viewAllPromos');
    
    if (promotionsGrid && viewAllPromosBtn) {
        initializeItems('promotions-grid', 'hidden-promo', 3);
        
        viewAllPromosBtn.addEventListener('click', function() {
            const promos = promotionsGrid.querySelectorAll('.promo-card');
            toggleItems(promos, this);
        });
    }
});