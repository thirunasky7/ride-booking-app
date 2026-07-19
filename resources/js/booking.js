const cfg = window.bookingConfig || {};

let priceTimer;

function isOthersTrip() {
    return document.getElementById('trip_type')?.value === 'others';
}

function isOtherApartment() {
    return document.getElementById('apartment_id')?.value === 'other';
}

function isOtherBusStand() {
    return document.getElementById('bus_stand_id')?.value === 'other';
}

function usesCustomAddresses() {
    return isOthersTrip();
}

function usesCustomPricing() {
    return isOthersTrip() || isOtherApartment() || isOtherBusStand();
}

function triggerPriceUpdate() {
    clearTimeout(priceTimer);
    priceTimer = setTimeout(fetchPrice, 400);
}

function showStep(n) {
    document.querySelectorAll('.booking-step-panel').forEach(p => p.classList.add('d-none'));
    document.getElementById('step' + n)?.classList.remove('d-none');
    document.querySelectorAll('.step-item').forEach(s => {
        s.classList.toggle('active', s.dataset.step === n);
    });
}

function selectedText(selectId) {
    const select = document.getElementById(selectId);
    if (!select || !select.value || select.value === 'other') return '—';
    return select.options[select.selectedIndex]?.text || '—';
}

function activePickupInput() {
    if (isOthersTrip()) return document.getElementById('pickup_address_custom');
    if (isOtherApartment()) return document.getElementById('pickup_address_standard');
    return null;
}

function activeDropInput() {
    if (isOthersTrip()) return document.getElementById('drop_address_custom');
    if (isOtherBusStand()) return document.getElementById('drop_address_standard');
    return null;
}

function syncFieldState() {
    const others = isOthersTrip();
    const standardRoute = document.getElementById('standardRouteFields');
    const customFields = document.getElementById('customAddressFields');
    const otherApartmentWrap = document.getElementById('otherApartmentWrap');
    const otherBusStandWrap = document.getElementById('otherBusStandWrap');

    standardRoute?.classList.toggle('d-none', others);
    customFields?.classList.toggle('d-none', !others);

    otherApartmentWrap?.classList.toggle('d-none', others || !isOtherApartment());
    otherBusStandWrap?.classList.toggle('d-none', others || !isOtherBusStand());

    const apartmentSelect = document.getElementById('apartment_id');
    const busStandSelect = document.getElementById('bus_stand_id');

    if (apartmentSelect) {
        apartmentSelect.required = !others;
        apartmentSelect.disabled = others;
    }

    if (busStandSelect) {
        busStandSelect.required = !others;
        busStandSelect.disabled = others;
    }

    const pickupStandard = document.getElementById('pickup_address_standard');
    const dropStandard = document.getElementById('drop_address_standard');
    const pickupCustom = document.getElementById('pickup_address_custom');
    const dropCustom = document.getElementById('drop_address_custom');

    if (pickupStandard) {
        pickupStandard.required = !others && isOtherApartment();
        pickupStandard.disabled = others || !isOtherApartment();
    }

    if (dropStandard) {
        dropStandard.required = !others && isOtherBusStand();
        dropStandard.disabled = others || !isOtherBusStand();
    }

    if (pickupCustom) {
        pickupCustom.required = others;
        pickupCustom.disabled = !others;
    }

    if (dropCustom) {
        dropCustom.required = others;
        dropCustom.disabled = !others;
    }
}

function validateStep1() {
    const date = document.getElementById('booking_date')?.value;
    const slot = document.getElementById('time_slot_id')?.value;

    if (!date || !slot) {
        alert('Please select a booking date and time slot.');
        return false;
    }

    if (isOthersTrip()) {
        const pickup = document.getElementById('pickup_address_custom')?.value.trim();
        const drop = document.getElementById('drop_address_custom')?.value.trim();
        if (!pickup || !drop) {
            alert('Please enter both pickup and drop addresses.');
            return false;
        }
        return true;
    }

    const apartment = document.getElementById('apartment_id')?.value;
    const busStand = document.getElementById('bus_stand_id')?.value;

    if (!apartment && !busStand) {
        alert('Please select apartment and bus stand, or choose Other to enter addresses.');
        return false;
    }

    if (apartment === 'other') {
        const pickup = document.getElementById('pickup_address_standard')?.value.trim();
        if (!pickup) {
            alert('Please enter a pickup address.');
            return false;
        }
    } else if (!apartment) {
        alert('Please select an apartment.');
        return false;
    }

    if (busStand === 'other') {
        const drop = document.getElementById('drop_address_standard')?.value.trim();
        if (!drop) {
            alert('Please enter a drop address.');
            return false;
        }
    } else if (!busStand) {
        alert('Please select a bus stand.');
        return false;
    }

    return true;
}

function updateSummary() {
    const pickupLabel = document.getElementById('sumPickupLabel');
    const dropLabel = document.getElementById('sumDropLabel');

    if (isOthersTrip()) {
        pickupLabel.textContent = 'Pickup';
        dropLabel.textContent = 'Drop';
        document.getElementById('sumPickup').textContent = document.getElementById('pickup_address_custom')?.value.trim() || '—';
        document.getElementById('sumDrop').textContent = document.getElementById('drop_address_custom')?.value.trim() || '—';
    } else {
        pickupLabel.textContent = isOtherApartment() ? 'Pickup' : 'Apartment';
        dropLabel.textContent = isOtherBusStand() ? 'Drop' : 'Bus Stand';
        document.getElementById('sumPickup').textContent = isOtherApartment()
            ? (document.getElementById('pickup_address_standard')?.value.trim() || '—')
            : selectedText('apartment_id');
        document.getElementById('sumDrop').textContent = isOtherBusStand()
            ? (document.getElementById('drop_address_standard')?.value.trim() || '—')
            : selectedText('bus_stand_id');
    }

    document.getElementById('sumTrip').textContent = selectedText('trip_type');
    document.getElementById('sumDate').textContent = document.getElementById('booking_date')?.value || '—';
    const slot = document.getElementById('time_slot_id');
    document.getElementById('sumTime').textContent = slot?.options[slot.selectedIndex]?.text || '—';
}

function formatTime(t) {
    const [h, m] = t.split(':');
    const hour = parseInt(h);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    return ((hour % 12) || 12) + ':' + m + ' ' + ampm;
}

async function loadSlots() {
    const date = document.getElementById('booking_date')?.value;
    const slotSelect = document.getElementById('time_slot_id');
    const slotHidden = document.getElementById('slot_time');
    if (!date || !cfg.availableSlotsUrl || !slotSelect) return;

    const loader = document.getElementById('slotsLoading');
    loader?.classList.remove('d-none');
    try {
        const res = await fetch(`${cfg.availableSlotsUrl}?booking_date=${date}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        const slots = json.data?.slots || json.slots || [];
        slotSelect.innerHTML = '';
        slots.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.time_slot_id;
            opt.dataset.slotTime = s.slot_time;
            opt.textContent = formatTime(s.slot_time) + (s.is_available ? '' : ' (Full)');
            opt.disabled = !s.is_available;
            slotSelect.appendChild(opt);
        });
        if (slotSelect.options.length && slotHidden) {
            slotHidden.value = slotSelect.options[0].dataset.slotTime;
        }
    } catch (e) {
        console.error(e);
    } finally {
        loader?.classList.add('d-none');
    }
}

async function fetchPrice() {
    const preview = document.getElementById('farePreview');
    const loading = document.getElementById('fareLoading');
    const icon = document.getElementById('fareIcon');
    const date = document.getElementById('booking_date')?.value;
    const slotTime = document.getElementById('slot_time')?.value;
    const tripType = document.getElementById('trip_type')?.value;

    if (!date || !slotTime) return;

    const params = new URLSearchParams({
        booking_date: date,
        slot_time: slotTime,
        trip_type: tripType || '',
        time_slot_id: document.getElementById('time_slot_id')?.value || '',
    });

    if (usesCustomPricing()) {
        const pickup = activePickupInput()?.value.trim();
        const drop = activeDropInput()?.value.trim();

        if (isOthersTrip()) {
            if (!pickup || !drop) return;
            params.set('pickup_address', pickup);
            params.set('drop_address', drop);
        } else {
            if (isOtherApartment()) {
                if (!pickup) return;
                params.set('pickup_address', pickup);
            } else {
                params.set('apartment_id', document.getElementById('apartment_id')?.value || '');
            }

            if (isOtherBusStand()) {
                if (!drop) return;
                params.set('drop_address', drop);
            } else {
                params.set('bus_stand_id', document.getElementById('bus_stand_id')?.value || '');
            }
        }
    } else {
        const apartmentId = document.getElementById('apartment_id')?.value;
        const busStandId = document.getElementById('bus_stand_id')?.value;
        if (!apartmentId || !busStandId) return;
        params.set('apartment_id', apartmentId);
        params.set('bus_stand_id', busStandId);
    }

    preview?.classList.remove('d-none');
    loading?.classList.remove('d-none');
    icon?.classList.add('d-none');

    try {
        const res = await fetch(`${cfg.calculatePriceUrl}?${params}`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const json = await res.json();
        if (json.status) {
            const fare = json.data.estimated_fare;
            document.getElementById('fareAmount').textContent = '₹' + Number(fare).toFixed(0);
            const fareType = json.data.booking_type === 'instant'
                ? 'Instant booking (today)' : 'Scheduled booking';
            document.getElementById('fareType').textContent = usesCustomPricing()
                ? fareType + ' · custom route'
                : fareType;
            document.getElementById('sumFare').textContent = '₹' + Number(fare).toFixed(0);
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading?.classList.add('d-none');
        icon?.classList.remove('d-none');
    }
}

function initBookingPage() {
    if (!document.getElementById('bookingForm')) return;

    syncFieldState();

    document.getElementById('trip_type')?.addEventListener('change', () => {
        syncFieldState();
        triggerPriceUpdate();
    });

    document.getElementById('apartment_id')?.addEventListener('change', () => {
        syncFieldState();
        triggerPriceUpdate();
    });

    document.getElementById('bus_stand_id')?.addEventListener('change', () => {
        syncFieldState();
        triggerPriceUpdate();
    });

    ['pickup_address_standard', 'drop_address_standard', 'pickup_address_custom', 'drop_address_custom']
        .forEach(id => {
            document.getElementById(id)?.addEventListener('input', triggerPriceUpdate);
        });

    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', () => {
            const next = btn.dataset.next;
            if (next === '2' && !validateStep1()) return;
            if (next === '2') {
                updateSummary();
                fetchPrice();
            }
            showStep(next);
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => showStep(btn.dataset.prev));
    });

    const slotSelect = document.getElementById('time_slot_id');
    const slotHidden = document.getElementById('slot_time');
    slotSelect?.addEventListener('change', () => {
        const opt = slotSelect.options[slotSelect.selectedIndex];
        if (slotHidden) slotHidden.value = opt?.dataset?.slotTime || '';
        triggerPriceUpdate();
    });

    document.getElementById('booking_date')?.addEventListener('change', () => {
        loadSlots();
        triggerPriceUpdate();
    });

    if (document.getElementById('booking_date')?.value) {
        loadSlots();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBookingPage);
} else {
    initBookingPage();
}
