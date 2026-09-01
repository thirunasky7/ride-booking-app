const cfg = window.bookingConfig || {};

let priceTimer;

function isOtherPickup() {
    return document.getElementById('pickup_location')?.value === 'other';
}

function isOtherDrop() {
    return document.getElementById('drop_location')?.value === 'other';
}

function usesCustomAddresses() {
    return isOtherPickup() || isOtherDrop();
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
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function selectedText(selectId) {
    const select = document.getElementById(selectId);
    if (!select || !select.value) return '—';
    if (select.value === 'other') return 'Custom address';
    return select.options[select.selectedIndex]?.text?.trim() || '—';
}

function syncCustomFields() {
    const show = usesCustomAddresses();
    const wrap = document.getElementById('customAddressFields');
    wrap?.classList.toggle('d-none', !show);

    const pickup = document.getElementById('pickup_address');
    const drop = document.getElementById('drop_address');
    if (pickup) pickup.required = isOtherPickup();
    if (drop) drop.required = isOtherDrop();
}

function syncSlotTime() {
    const slotSelect = document.getElementById('time_slot_id');
    const slotHidden = document.getElementById('slot_time');
    if (!slotSelect || !slotHidden) return;

    const opt = slotSelect.options[slotSelect.selectedIndex];
    if (opt?.dataset?.slotTime) {
        slotHidden.value = opt.dataset.slotTime;
    }
}

function validateStep1() {
    syncSlotTime();

    const pickup = document.getElementById('pickup_location')?.value;
    const drop = document.getElementById('drop_location')?.value;
    const date = document.getElementById('booking_date')?.value;
    const slot = document.getElementById('time_slot_id')?.value;

    if (!pickup) { alert('Please select a pickup location.'); return false; }
    if (!drop) { alert('Please select a drop location.'); return false; }
    if (pickup === drop && pickup !== 'other') { alert('Pickup and drop cannot be the same.'); return false; }
    if (!date || !slot) { alert('Please select date and time slot.'); return false; }

    if (isOtherPickup() && !document.getElementById('pickup_address')?.value.trim()) {
        alert('Please enter pickup address.');
        return false;
    }
    if (isOtherDrop() && !document.getElementById('drop_address')?.value.trim()) {
        alert('Please enter drop address.');
        return false;
    }

    return true;
}

function updateSummary() {
    document.getElementById('sumPickup').textContent = isOtherPickup()
        ? (document.getElementById('pickup_address')?.value.trim() || 'Custom address')
        : selectedText('pickup_location');
    document.getElementById('sumDrop').textContent = isOtherDrop()
        ? (document.getElementById('drop_address')?.value.trim() || 'Custom address')
        : selectedText('drop_location');
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
        const current = slotSelect.value;
        slotSelect.innerHTML = '';
        slots.forEach(s => {
            const opt = document.createElement('option');
            opt.value = s.time_slot_id;
            opt.dataset.slotTime = s.slot_time;
            opt.textContent = formatTime(s.slot_time) + (s.is_available ? '' : ' (Full)');
            opt.disabled = !s.is_available;
            slotSelect.appendChild(opt);
        });
        if (current) slotSelect.value = current;
        if (slotSelect.options.length && slotHidden) {
            const opt = slotSelect.options[slotSelect.selectedIndex];
            slotHidden.value = opt?.dataset?.slotTime || '';
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
    const pickup = document.getElementById('pickup_location')?.value;
    const drop = document.getElementById('drop_location')?.value;
    const date = document.getElementById('booking_date')?.value;
    const slotTime = document.getElementById('slot_time')?.value;

    if (!pickup || !drop || !date || !slotTime) return;

    const params = new URLSearchParams({
        pickup_location: pickup,
        drop_location: drop,
        booking_date: date,
        slot_time: slotTime,
        time_slot_id: document.getElementById('time_slot_id')?.value || '',
    });

    if (usesCustomAddresses()) {
        if (isOtherPickup()) params.set('pickup_address', document.getElementById('pickup_address')?.value.trim() || '');
        if (isOtherDrop()) params.set('drop_address', document.getElementById('drop_address')?.value.trim() || '');
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
            document.getElementById('fareType').textContent = json.data.booking_type === 'instant'
                ? 'Instant booking (today)' : 'Scheduled booking';
            document.getElementById('sumFare').textContent = '₹' + Number(fare).toFixed(0);
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading?.classList.add('d-none');
        icon?.classList.remove('d-none');
    }
}

function getFormData() {
    const form = document.getElementById('bookingForm');
    return new FormData(form);
}

async function submitBooking() {
    syncSlotTime();

    const btn = document.getElementById('payAndBookBtn');
    const errBox = document.getElementById('paymentError');
    errBox?.classList.add('d-none');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing…';

    try {
        const res = await fetch(cfg.storeBookingUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': cfg.csrfToken,
            },
            body: getFormData(),
        });
        const json = await res.json();

        if (!json.status) {
            throw new Error(json.message || 'Booking failed. Please try again.');
        }

        if (cfg.razorpayEnabled && json.data?.payment) {
            openRazorpay(json.data.payment, json.data.booking_id);
        } else {
            window.location.href = json.data?.redirect || cfg.myBookingsUrl;
        }
    } catch (e) {
        errBox.textContent = e.message;
        errBox.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        btn.innerHTML = cfg.razorpayEnabled
            ? '<i class="bi bi-credit-card me-1"></i> Pay & Confirm Booking'
            : '<i class="bi bi-check-lg me-1"></i> Confirm Booking';
    }
}

function openRazorpay(payment, bookingId) {
    const options = {
        key: payment.key_id || cfg.razorpayKeyId,
        amount: payment.amount,
        currency: payment.currency,
        name: 'Apartment Shuttle',
        description: 'Ride booking payment',
        order_id: payment.order_id,
        prefill: {
            name: payment.customer_name || '',
            contact: payment.customer_mobile || '',
        },
        theme: { color: '#0f766e' },
        handler: async function (response) {
            try {
                const verifyRes = await fetch(cfg.verifyPaymentUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': cfg.csrfToken,
                    },
                    body: JSON.stringify({
                        booking_id: bookingId,
                        razorpay_order_id: response.razorpay_order_id,
                        razorpay_payment_id: response.razorpay_payment_id,
                        razorpay_signature: response.razorpay_signature,
                    }),
                });
                const verifyJson = await verifyRes.json();
                if (verifyJson.status) {
                    window.location.href = verifyJson.data?.redirect || cfg.myBookingsUrl;
                } else {
                    throw new Error(verifyJson.message || 'Payment verification failed.');
                }
            } catch (e) {
                const errBox = document.getElementById('paymentError');
                errBox.textContent = e.message;
                errBox.classList.remove('d-none');
            }
        },
        modal: {
            ondismiss: function () {
                document.getElementById('paymentError').textContent = 'Payment was cancelled. Your booking is saved as pending — you can try again from My Bookings.';
                document.getElementById('paymentError').classList.remove('d-none');
            },
        },
    };

    const rzp = new Razorpay(options);
    rzp.open();
}

function initBookingPage() {
    if (!document.getElementById('bookingForm')) return;

    syncCustomFields();

    document.getElementById('pickup_location')?.addEventListener('change', () => {
        syncCustomFields();
        triggerPriceUpdate();
    });
    document.getElementById('drop_location')?.addEventListener('change', () => {
        syncCustomFields();
        triggerPriceUpdate();
    });
    ['pickup_address', 'drop_address'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', triggerPriceUpdate);
    });

    document.getElementById('swapLocations')?.addEventListener('click', () => {
        const pickup = document.getElementById('pickup_location');
        const drop = document.getElementById('drop_location');
        const tmp = pickup.value;
        pickup.value = drop.value;
        drop.value = tmp;
        syncCustomFields();
        triggerPriceUpdate();
    });

    document.querySelectorAll('.btn-next').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!validateStep1()) return;
            updateSummary();
            fetchPrice();
            showStep(btn.dataset.next);
        });
    });

    document.querySelectorAll('.btn-prev').forEach(btn => {
        btn.addEventListener('click', () => showStep(btn.dataset.prev));
    });

    document.getElementById('payAndBookBtn')?.addEventListener('click', submitBooking);

    const slotSelect = document.getElementById('time_slot_id');
    const slotHidden = document.getElementById('slot_time');
    slotSelect?.addEventListener('change', () => {
        syncSlotTime();
        triggerPriceUpdate();
    });

    document.getElementById('booking_date')?.addEventListener('change', () => {
        loadSlots();
        triggerPriceUpdate();
    });

    if (document.getElementById('booking_date')?.value) {
        loadSlots().then(() => syncSlotTime());
    } else {
        syncSlotTime();
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBookingPage);
} else {
    initBookingPage();
}
