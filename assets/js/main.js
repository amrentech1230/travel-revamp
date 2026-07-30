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

    // ═══ Airport Autocomplete (AJAX from Database) ═══
    const AIRPORT_SEARCH_URL = (document.querySelector('meta[name="base-path"]')?.content || '/travel-revamp') + '/ajax/airports.php';
    let searchTimeout = null;

    function setupAutocomplete(inputId, codeId, suggestionsId) {
        const input = document.getElementById(inputId);
        const code = document.getElementById(codeId);
        const suggestions = document.getElementById(suggestionsId);
        if (!input || !suggestions) return;

        let activeIndex = -1;

        input.addEventListener('input', () => {
            const query = input.value.trim();
            activeIndex = -1;

            if (query.length < 2) {
                suggestions.classList.remove('active');
                return;
            }

            // Debounce: wait 300ms after user stops typing before making AJAX call
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetchAirports(query, input, code, suggestions);
            }, 300);
        });

        // Keyboard navigation for suggestions
        input.addEventListener('keydown', (e) => {
            const items = suggestions.querySelectorAll('.airport-item');
            if (!items.length || !suggestions.classList.contains('active')) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                activeIndex = Math.min(activeIndex + 1, items.length - 1);
                highlightItem(items, activeIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                activeIndex = Math.max(activeIndex - 1, 0);
                highlightItem(items, activeIndex);
            } else if (e.key === 'Enter' && activeIndex >= 0) {
                e.preventDefault();
                items[activeIndex].click();
            } else if (e.key === 'Escape') {
                suggestions.classList.remove('active');
                activeIndex = -1;
            }
        });

        input.addEventListener('blur', () => {
            setTimeout(() => {
                suggestions.classList.remove('active');
                activeIndex = -1;
            }, 200);
        });

        input.addEventListener('focus', () => {
            if (input.value.trim().length >= 2 && suggestions.children.length > 0) {
                suggestions.classList.add('active');
            }
        });
    }

    function fetchAirports(query, input, code, suggestions) {
        fetch(AIRPORT_SEARCH_URL + '?q=' + encodeURIComponent(query))
            .then(response => {
                if (!response.ok) throw new Error('Search failed');
                return response.json();
            })
            .then(results => {
                if (!results || results.length === 0) {
                    suggestions.innerHTML = '<div class="airport-item no-results"><small>No airports found</small></div>';
                    suggestions.classList.add('active');
                    return;
                }

                suggestions.innerHTML = results.map(a => `
                    <div class="airport-item" data-iata="${a.iata}" data-city="${a.city}" data-country="${a.country}">
                        <div class="airport-info">
                            <strong>${a.city}</strong>, ${a.country}
                            <br><small>${a.name}</small>
                        </div>
                        <span class="code">${a.iata}</span>
                    </div>
                `).join('');
                suggestions.classList.add('active');

                suggestions.querySelectorAll('.airport-item:not(.no-results)').forEach(item => {
                    item.addEventListener('click', () => {
                        input.value = item.dataset.city + ' (' + item.dataset.iata + ')';
                        code.value = item.dataset.iata;
                        suggestions.classList.remove('active');
                    });
                });
            })
            .catch(err => {
                console.error('Airport search error:', err);
                suggestions.classList.remove('active');
            });
    }

    function highlightItem(items, index) {
        items.forEach(item => item.classList.remove('highlighted'));
        if (items[index]) {
            items[index].classList.add('highlighted');
            items[index].scrollIntoView({ block: 'nearest' });
        }
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
