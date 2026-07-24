/**
 * TravenzoTravel - Main JavaScript
 * Handles interactivity for flight search, filters, mobile menu, etc.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ═══ Mobile Menu ═══
    const mobileToggle = document.getElementById('mobileToggle');
    const mobileSidebar = document.getElementById('mobileSidebar');
    const mobileClose = document.getElementById('mobileClose');
    const mobileOverlay = document.getElementById('mobileOverlay');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', () => {
            mobileSidebar.classList.add('active');
            mobileOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    [mobileClose, mobileOverlay].forEach(el => {
        if (el) el.addEventListener('click', () => {
            mobileSidebar.classList.remove('active');
            mobileOverlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    });

    // ═══ User Dropdown ═══
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    if (userMenuBtn) {
        userMenuBtn.addEventListener('click', () => {
            userDropdown.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.user-menu')) {
                userDropdown?.classList.remove('active');
            }
        });
    }


    // ═══ Trip Type Toggle (Show/Hide Return Date) ═══
    const tripRadios = document.querySelectorAll('input[name="trip_type"]');
    const returnField = document.getElementById('returnField');

    tripRadios.forEach(radio => {
        radio.addEventListener('change', () => {
            if (returnField) {
                returnField.style.display = radio.value === 'roundtrip' ? 'block' : 'none';
            }
        });
    });

    // ═══ Swap Cities ═══
    const swapBtn = document.getElementById('swapBtn');
    if (swapBtn) {
        swapBtn.addEventListener('click', () => {
            const originInput = document.getElementById('originInput');
            const destInput = document.getElementById('destInput');
            const originCode = document.getElementById('originCode');
            const destCode = document.getElementById('destCode');

            const tempVal = originInput.value;
            const tempCode = originCode.value;

            originInput.value = destInput.value;
            originCode.value = destCode.value;
            destInput.value = tempVal;
            destCode.value = tempCode;
        });
    }

    // ═══ Travelers Dropdown ═══
    const travelersTrigger = document.getElementById('travelersTrigger');
    const travelersDropdown = document.getElementById('travelersDropdown');
    const applyPax = document.getElementById('applyPax');

    if (travelersTrigger) {
        travelersTrigger.addEventListener('click', () => {
            travelersDropdown.classList.toggle('active');
        });
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.field-travelers')) {
                travelersDropdown?.classList.remove('active');
            }
        });
    }

    // Passenger counter buttons
    document.querySelectorAll('.pax-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            if (!target) return;
            let val = parseInt(target.value);
            if (btn.classList.contains('plus')) {
                if (val < parseInt(target.max)) target.value = val + 1;
            } else {
                if (val > parseInt(target.min)) target.value = val - 1;
            }
            updateTravelersText();
        });
    });

    if (applyPax) {
        applyPax.addEventListener('click', () => {
            travelersDropdown.classList.remove('active');
        });
    }

    function updateTravelersText() {
        const adults = parseInt(document.getElementById('adults')?.value || 1);
        const children = parseInt(document.getElementById('children')?.value || 0);
        const infants = parseInt(document.getElementById('infants')?.value || 0);
        const cabin = document.getElementById('cabinClass')?.selectedOptions[0]?.text || 'Economy';
        const total = adults + children + infants;
        const text = document.getElementById('travelersText');
        if (text) {
            text.textContent = `${total} Traveler${total > 1 ? 's' : ''}, ${cabin}`;
        }
    }

    // ═══ Airport Autocomplete ═══
    const airports = [
        // India
        { iata: 'DEL', city: 'New Delhi', name: 'Indira Gandhi International' },
        { iata: 'BOM', city: 'Mumbai', name: 'Chhatrapati Shivaji Maharaj' },
        { iata: 'BLR', city: 'Bangalore', name: 'Kempegowda International' },
        { iata: 'MAA', city: 'Chennai', name: 'Chennai International' },
        { iata: 'CCU', city: 'Kolkata', name: 'Netaji Subhas Chandra Bose' },
        { iata: 'HYD', city: 'Hyderabad', name: 'Rajiv Gandhi International' },
        { iata: 'GOI', city: 'Goa', name: 'Dabolim Airport' },
        { iata: 'AMD', city: 'Ahmedabad', name: 'Sardar Vallabhbhai Patel' },
        { iata: 'PNQ', city: 'Pune', name: 'Pune Airport' },
        { iata: 'COK', city: 'Kochi', name: 'Cochin International' },
        { iata: 'JAI', city: 'Jaipur', name: 'Jaipur International' },
        { iata: 'LKO', city: 'Lucknow', name: 'Chaudhary Charan Singh' },
        { iata: 'IXC', city: 'Chandigarh', name: 'Chandigarh International' },
        { iata: 'TRV', city: 'Thiruvananthapuram', name: 'Trivandrum International' },
        { iata: 'VNS', city: 'Varanasi', name: 'Lal Bahadur Shastri' },
        { iata: 'SXR', city: 'Srinagar', name: 'Sheikh ul-Alam International' },
        { iata: 'GAU', city: 'Guwahati', name: 'Lokpriya Gopinath Bordoloi' },
        { iata: 'BBI', city: 'Bhubaneswar', name: 'Biju Patnaik International' },
        // United States
        { iata: 'JFK', city: 'New York', name: 'John F. Kennedy International' },
        { iata: 'LAX', city: 'Los Angeles', name: 'Los Angeles International' },
        { iata: 'ORD', city: 'Chicago', name: "O'Hare International" },
        { iata: 'SFO', city: 'San Francisco', name: 'San Francisco International' },
        { iata: 'ATL', city: 'Atlanta', name: 'Hartsfield-Jackson International' },
        { iata: 'DFW', city: 'Dallas', name: 'Dallas/Fort Worth International' },
        { iata: 'MIA', city: 'Miami', name: 'Miami International' },
        { iata: 'BOS', city: 'Boston', name: 'Boston Logan International' },
        { iata: 'SEA', city: 'Seattle', name: 'Seattle-Tacoma International' },
        { iata: 'DEN', city: 'Denver', name: 'Denver International' },
        { iata: 'LAS', city: 'Las Vegas', name: 'Harry Reid International' },
        { iata: 'EWR', city: 'Newark', name: 'Newark Liberty International' },
        { iata: 'IAD', city: 'Washington DC', name: 'Washington Dulles International' },
        // Europe
        { iata: 'LHR', city: 'London', name: 'Heathrow Airport' },
        { iata: 'LGW', city: 'London', name: 'Gatwick Airport' },
        { iata: 'CDG', city: 'Paris', name: 'Charles de Gaulle' },
        { iata: 'FRA', city: 'Frankfurt', name: 'Frankfurt Airport' },
        { iata: 'AMS', city: 'Amsterdam', name: 'Schiphol Airport' },
        { iata: 'BCN', city: 'Barcelona', name: 'Barcelona-El Prat' },
        { iata: 'MAD', city: 'Madrid', name: 'Madrid-Barajas' },
        { iata: 'FCO', city: 'Rome', name: 'Leonardo da Vinci' },
        { iata: 'MXP', city: 'Milan', name: 'Milan Malpensa' },
        { iata: 'MUC', city: 'Munich', name: 'Munich Airport' },
        { iata: 'ZRH', city: 'Zurich', name: 'Zurich Airport' },
        { iata: 'IST', city: 'Istanbul', name: 'Istanbul Airport' },
        { iata: 'ATH', city: 'Athens', name: 'Athens International' },
        { iata: 'VIE', city: 'Vienna', name: 'Vienna International' },
        { iata: 'DUB', city: 'Dublin', name: 'Dublin Airport' },
        { iata: 'CPH', city: 'Copenhagen', name: 'Copenhagen Airport' },
        { iata: 'LIS', city: 'Lisbon', name: 'Lisbon Portela' },
        // Middle East
        { iata: 'DXB', city: 'Dubai', name: 'Dubai International' },
        { iata: 'AUH', city: 'Abu Dhabi', name: 'Abu Dhabi International' },
        { iata: 'DOH', city: 'Doha', name: 'Hamad International' },
        { iata: 'RUH', city: 'Riyadh', name: 'King Khalid International' },
        { iata: 'JED', city: 'Jeddah', name: 'King Abdulaziz International' },
        { iata: 'BAH', city: 'Manama', name: 'Bahrain International' },
        { iata: 'MCT', city: 'Muscat', name: 'Muscat International' },
        { iata: 'KWI', city: 'Kuwait City', name: 'Kuwait International' },
        // Asia Pacific
        { iata: 'SIN', city: 'Singapore', name: 'Changi Airport' },
        { iata: 'BKK', city: 'Bangkok', name: 'Suvarnabhumi Airport' },
        { iata: 'HKG', city: 'Hong Kong', name: 'Hong Kong International' },
        { iata: 'NRT', city: 'Tokyo', name: 'Narita International' },
        { iata: 'HND', city: 'Tokyo', name: 'Haneda Airport' },
        { iata: 'ICN', city: 'Seoul', name: 'Incheon International' },
        { iata: 'PEK', city: 'Beijing', name: 'Beijing Capital International' },
        { iata: 'PVG', city: 'Shanghai', name: 'Shanghai Pudong International' },
        { iata: 'KUL', city: 'Kuala Lumpur', name: 'KLIA International' },
        { iata: 'CGK', city: 'Jakarta', name: 'Soekarno-Hatta International' },
        { iata: 'DPS', city: 'Bali', name: 'Ngurah Rai International' },
        { iata: 'MNL', city: 'Manila', name: 'Ninoy Aquino International' },
        { iata: 'TPE', city: 'Taipei', name: 'Taiwan Taoyuan International' },
        { iata: 'CMB', city: 'Colombo', name: 'Bandaranaike International' },
        { iata: 'DAC', city: 'Dhaka', name: 'Hazrat Shahjalal International' },
        { iata: 'KTM', city: 'Kathmandu', name: 'Tribhuvan International' },
        // Australia
        { iata: 'SYD', city: 'Sydney', name: 'Sydney Kingsford Smith' },
        { iata: 'MEL', city: 'Melbourne', name: 'Melbourne Tullamarine' },
        { iata: 'AKL', city: 'Auckland', name: 'Auckland Airport' },
        // Africa
        { iata: 'JNB', city: 'Johannesburg', name: 'O.R. Tambo International' },
        { iata: 'CAI', city: 'Cairo', name: 'Cairo International' },
        { iata: 'NBO', city: 'Nairobi', name: 'Jomo Kenyatta International' },
        // Americas
        { iata: 'YYZ', city: 'Toronto', name: 'Toronto Pearson International' },
        { iata: 'YVR', city: 'Vancouver', name: 'Vancouver International' },
        { iata: 'MEX', city: 'Mexico City', name: 'Mexico City International' },
        { iata: 'CUN', city: 'Cancun', name: 'Cancun International' },
        { iata: 'GRU', city: 'Sao Paulo', name: 'Guarulhos International' },
        { iata: 'EZE', city: 'Buenos Aires', name: 'Ministro Pistarini International' },
    ];

    function setupAutocomplete(inputId, codeId, suggestionsId) {
        const input = document.getElementById(inputId);
        const code = document.getElementById(codeId);
        const suggestions = document.getElementById(suggestionsId);
        if (!input || !suggestions) return;

        input.addEventListener('input', () => {
            const query = input.value.toLowerCase().trim();
            if (query.length < 2) { suggestions.classList.remove('active'); return; }

            const matches = airports.filter(a =>
                a.iata.toLowerCase().includes(query) ||
                a.city.toLowerCase().includes(query) ||
                a.name.toLowerCase().includes(query)
            ).slice(0, 8);

            if (matches.length === 0) { suggestions.classList.remove('active'); return; }

            suggestions.innerHTML = matches.map(a => `
                <div class="airport-item" data-iata="${a.iata}" data-city="${a.city}">
                    <div><strong>${a.city}</strong><br><small>${a.name}</small></div>
                    <span class="code">${a.iata}</span>
                </div>
            `).join('');
            suggestions.classList.add('active');

            suggestions.querySelectorAll('.airport-item').forEach(item => {
                item.addEventListener('click', () => {
                    input.value = item.dataset.city + ' (' + item.dataset.iata + ')';
                    code.value = item.dataset.iata;
                    suggestions.classList.remove('active');
                });
            });
        });

        input.addEventListener('blur', () => {
            setTimeout(() => suggestions.classList.remove('active'), 200);
        });
    }

    setupAutocomplete('originInput', 'originCode', 'originSuggestions');
    setupAutocomplete('destInput', 'destCode', 'destSuggestions');


    // ═══ Flight Details Toggle (Search Results) ═══
    document.querySelectorAll('.fc-details-toggle, button.fc-details-toggle').forEach(btn => {
        btn.addEventListener('click', () => {
            const idx = btn.dataset.idx;
            const details = document.getElementById('fcDetails-' + idx);
            if (details) {
                const isVisible = details.style.display !== 'none';
                details.style.display = isVisible ? 'none' : 'block';
                const icon = btn.querySelector('i');
                if (icon) icon.className = isVisible ? 'fas fa-chevron-down' : 'fas fa-chevron-up';
            }
        });
    });

    // ═══ Sort Buttons (Search Results) ═══
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sort-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const sortBy = btn.dataset.sort;
            const flightList = document.getElementById('flightList');
            if (!flightList) return;

            const cards = Array.from(flightList.querySelectorAll('.flight-card'));
            cards.sort((a, b) => {
                if (sortBy === 'price') return parseFloat(a.dataset.price) - parseFloat(b.dataset.price);
                if (sortBy === 'duration') return parseFloat(a.dataset.duration) - parseFloat(b.dataset.duration);
                if (sortBy === 'departure') return a.dataset.departure.localeCompare(b.dataset.departure);
                return 0;
            });
            cards.forEach(card => flightList.appendChild(card));
        });
    });

    // ═══ Price Range Filter ═══
    const priceSlider = document.getElementById('priceSlider');
    const priceVal = document.getElementById('priceVal');
    if (priceSlider) {
        priceSlider.addEventListener('input', () => {
            priceVal.textContent = '$' + priceSlider.value;
            filterFlights();
        });
    }

    // ═══ Checkbox Filters ═══
    document.querySelectorAll('.stop-filter, .time-filter, .airline-filter').forEach(cb => {
        cb.addEventListener('change', filterFlights);
    });

    function filterFlights() {
        const maxPrice = priceSlider ? parseInt(priceSlider.value) : 99999;
        const allowedStops = Array.from(document.querySelectorAll('.stop-filter:checked')).map(c => c.value);
        const allowedAirlines = Array.from(document.querySelectorAll('.airline-filter:checked')).map(c => c.value);

        document.querySelectorAll('.flight-card').forEach(card => {
            const price = parseFloat(card.dataset.price);
            const stops = card.dataset.stops;
            const airline = card.dataset.airline;

            let show = true;
            if (price > maxPrice) show = false;
            if (allowedStops.length && !allowedStops.includes(stops) && !allowedStops.includes(stops >= 2 ? '2' : stops)) show = false;
            if (allowedAirlines.length && !allowedAirlines.includes(airline)) show = false;

            card.style.display = show ? '' : 'none';
        });
    }

    // ═══ Clear Filters ═══
    const clearBtn = document.getElementById('clearFilters');
    if (clearBtn) {
        clearBtn.addEventListener('click', () => {
            document.querySelectorAll('.stop-filter, .time-filter, .airline-filter').forEach(cb => cb.checked = true);
            if (priceSlider) { priceSlider.value = priceSlider.max; priceVal.textContent = '$' + priceSlider.max; }
            filterFlights();
        });
    }

    // ═══ Mobile Filter Toggle ═══
    const mobileFilterBtn = document.getElementById('mobileFilterBtn');
    const filtersPanel = document.getElementById('filtersPanel');
    if (mobileFilterBtn && filtersPanel) {
        mobileFilterBtn.addEventListener('click', () => filtersPanel.classList.toggle('active'));
    }

    // ═══ Toggle Password Visibility ═══
    document.querySelectorAll('.toggle-pass').forEach(btn => {
        btn.addEventListener('click', () => {
            const target = document.getElementById(btn.dataset.target);
            if (target) {
                const isPassword = target.type === 'password';
                target.type = isPassword ? 'text' : 'password';
                btn.querySelector('i').className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
            }
        });
    });

    // ═══ Back to Top Button ═══
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
        window.addEventListener('scroll', () => {
            backToTop.classList.toggle('visible', window.scrollY > 400);
        });
        backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
    }

    // ═══ Flash Message Auto-dismiss ═══
    const flashMsg = document.getElementById('flashMsg');
    if (flashMsg) {
        setTimeout(() => flashMsg.remove(), 5000);
    }

    // ═══ Card Number Formatting ═══
    const cardInput = document.getElementById('cardNumber');
    if (cardInput) {
        cardInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = value;
        });
    }

    // ═══ Form Validation Feedback ═══
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (e) => {
            const requiredFields = form.querySelectorAll('[required]');
            let valid = true;
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = '#dc3545';
                    valid = false;
                } else {
                    field.style.borderColor = '';
                }
            });
            if (!valid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });

    // ═══ Newsletter Form (AJAX) ═══
    const nlForm = document.getElementById('newsletterForm');
    if (nlForm) {
        nlForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = nlForm.querySelector('input[name="email"]').value;
            if (!email) return;
            // Simple visual feedback
            nlForm.innerHTML = '<p style="color:#fff;font-size:14px;"><i class="fas fa-check-circle"></i> Thank you! You\'re subscribed.</p>';
        });
    }

}); // End DOMContentLoaded
