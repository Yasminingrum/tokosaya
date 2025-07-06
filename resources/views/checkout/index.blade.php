@extends('layouts.checkout')

@section('title', 'Checkout - TokoSaya')
@section('meta_description', 'Complete your purchase securely with multiple payment options and fast shipping')

@section('content')
<div class="checkout-container">
    <!-- Progress Indicator -->
    <div class="checkout-progress mb-4">
        <div class="container">
            <div class="progress-steps d-flex justify-content-between">
                <div class="step" :class="{'active': step >= 1, 'completed': step > 1}">
                    <div class="step-number">1</div>
                    <div class="step-label">Shipping</div>
                </div>
                <div class="step" :class="{'active': step >= 2, 'completed': step > 2}">
                    <div class="step-number">2</div>
                    <div class="step-label">Payment</div>
                </div>
                <div class="step" :class="{'active': step >= 3}">
                    <div class="step-number">3</div>
                    <div class="step-label">Review</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row">
            <!-- Main Checkout Form -->
            <div class="col-lg-8">
                <div class="checkout-form">
                    <form id="checkoutForm" @submit.prevent="processCheckout" x-data="checkoutData">
                        <!-- Step 1: Shipping Information -->
                        <div class="checkout-step" x-show="step === 1" x-transition>
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h4 class="mb-0">
                                        <i class="fas fa-shipping-fast text-primary me-2"></i>
                                        Shipping Information
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <!-- Saved Addresses -->
                                    @if(auth()->check() && auth()->user()->addresses->count() > 0)
                                    <div class="saved-addresses mb-4">
                                        <h6 class="fw-bold mb-3">Choose from saved addresses:</h6>
                                        <div class="row">
                                            @foreach(auth()->user()->addresses as $address)
                                            <div class="col-md-6 mb-3">
                                                <div class="address-card"
                                                     :class="{'selected': selectedAddress == {{ $address->id }}}"
                                                     @click="selectAddress({{ $address->id }})">
                                                    <div class="address-label">
                                                        <strong>{{ $address->label }}</strong>
                                                        @if($address->is_default)
                                                            <span class="badge bg-primary">Default</span>
                                                        @endif
                                                    </div>
                                                    <div class="address-details">
                                                        <p class="mb-1">{{ $address->recipient_name }}</p>
                                                        <p class="mb-1">{{ $address->phone }}</p>
                                                        <p class="mb-1">{{ $address->address_line1 }}</p>
                                                        @if($address->address_line2)
                                                            <p class="mb-1">{{ $address->address_line2 }}</p>
                                                        @endif
                                                        <p class="mb-0">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="text-center">
                                            <button type="button" class="btn btn-outline-primary" @click="showNewAddressForm = !showNewAddressForm">
                                                <i class="fas fa-plus me-2"></i>Add New Address
                                            </button>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- New Address Form -->
                                    <div class="new-address-form"
                                         x-show="showNewAddressForm || !hasSavedAddresses"
                                         x-transition>
                                        <h6 class="fw-bold mb-3">Shipping Address:</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="recipient_name" class="form-label">Recipient Name *</label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="recipient_name"
                                                       name="recipient_name"
                                                       x-model="shipping.recipient_name"
                                                       required>
                                                <div class="invalid-feedback" x-show="errors.recipient_name" x-text="errors.recipient_name"></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="phone" class="form-label">Phone Number *</label>
                                                <input type="tel"
                                                       class="form-control"
                                                       id="phone"
                                                       name="phone"
                                                       x-model="shipping.phone"
                                                       placeholder="08xx-xxxx-xxxx"
                                                       required>
                                                <div class="invalid-feedback" x-show="errors.phone" x-text="errors.phone"></div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address_line1" class="form-label">Address Line 1 *</label>
                                            <textarea class="form-control"
                                                      id="address_line1"
                                                      name="address_line1"
                                                      x-model="shipping.address_line1"
                                                      rows="2"
                                                      placeholder="Street address, apartment number, etc."
                                                      required></textarea>
                                            <div class="invalid-feedback" x-show="errors.address_line1" x-text="errors.address_line1"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address_line2" class="form-label">Address Line 2</label>
                                            <input type="text"
                                                   class="form-control"
                                                   id="address_line2"
                                                   name="address_line2"
                                                   x-model="shipping.address_line2"
                                                   placeholder="Landmark, building name (optional)">
                                        </div>
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label for="city" class="form-label">City *</label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="city"
                                                       name="city"
                                                       x-model="shipping.city"
                                                       required>
                                                <div class="invalid-feedback" x-show="errors.city" x-text="errors.city"></div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="state" class="form-label">State/Province *</label>
                                                <select class="form-select"
                                                        id="state"
                                                        name="state"
                                                        x-model="shipping.state"
                                                        required>
                                                    <option value="">Select State</option>
                                                    <option value="Jawa Barat">Jawa Barat</option>
                                                    <option value="Jawa Tengah">Jawa Tengah</option>
                                                    <option value="Jawa Timur">Jawa Timur</option>
                                                    <option value="DKI Jakarta">DKI Jakarta</option>
                                                    <option value="Sumatera Utara">Sumatera Utara</option>
                                                    <!-- Add more states -->
                                                </select>
                                                <div class="invalid-feedback" x-show="errors.state" x-text="errors.state"></div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label for="postal_code" class="form-label">Postal Code *</label>
                                                <input type="text"
                                                       class="form-control"
                                                       id="postal_code"
                                                       name="postal_code"
                                                       x-model="shipping.postal_code"
                                                       pattern="[0-9]{5}"
                                                       maxlength="5"
                                                       required>
                                                <div class="invalid-feedback" x-show="errors.postal_code" x-text="errors.postal_code"></div>
                                            </div>
                                        </div>

                                        @auth
                                        <div class="form-check mb-3">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   id="save_address"
                                                   x-model="saveAddress">
                                            <label class="form-check-label" for="save_address">
                                                Save this address for future orders
                                            </label>
                                        </div>
                                        @endauth
                                    </div>

                                    <!-- Shipping Methods -->
                                    <div class="shipping-methods mt-4" x-show="showShippingMethods">
                                        <h6 class="fw-bold mb-3">Shipping Method:</h6>
                                        <div class="shipping-options">
                                            <template x-for="method in shippingMethods" :key="method.id">
                                                <div class="shipping-option"
                                                     :class="{'selected': selectedShippingMethod == method.id}"
                                                     @click="selectShippingMethod(method.id, method.cost)">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="method-info">
                                                            <div class="method-name" x-text="method.name"></div>
                                                            <div class="method-description text-muted" x-text="method.description"></div>
                                                            <div class="method-estimate text-muted" x-text="`Estimated: ${method.estimate_min}-${method.estimate_max} days`"></div>
                                                        </div>
                                                        <div class="method-cost fw-bold" x-text="formatCurrency(method.cost)"></div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    <button type="button"
                                            class="btn btn-primary btn-lg"
                                            @click="goToStep(2)"
                                            :disabled="!canProceedToPayment">
                                        Continue to Payment
                                        <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Payment Information -->
                        <div class="checkout-step" x-show="step === 2" x-transition>
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h4 class="mb-0">
                                        <i class="fas fa-credit-card text-primary me-2"></i>
                                        Payment Information
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <!-- Payment Methods -->
                                    <div class="payment-methods">
                                        <h6 class="fw-bold mb-3">Select Payment Method:</h6>
                                        <div class="payment-options">
                                            @foreach($paymentMethods as $method)
                                            <div class="payment-option"
                                                 :class="{'selected': selectedPaymentMethod == '{{ $method->code }}'}"
                                                 @click="selectPaymentMethod('{{ $method->code }}', {{ $method->fee_amount_cents }})">
                                                <div class="d-flex align-items-center">
                                                    <div class="payment-logo me-3">
                                                        @if($method->logo)
                                                            <img src="{{ $method->logo }}" alt="{{ $method->name }}" class="payment-logo-img">
                                                        @else
                                                            <i class="fas fa-credit-card fa-2x text-primary"></i>
                                                        @endif
                                                    </div>
                                                    <div class="payment-info flex-grow-1">
                                                        <div class="payment-name fw-bold">{{ $method->name }}</div>
                                                        <div class="payment-description text-muted">{{ $method->description }}</div>
                                                        @if($method->fee_amount_cents > 0)
                                                            <div class="payment-fee text-warning">
                                                                + {{ format_currency($method->fee_amount_cents) }} fee
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="payment-radio">
                                                        <i class="fas fa-check-circle text-success"
                                                           x-show="selectedPaymentMethod == '{{ $method->code }}'"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Payment Details -->
                                    <div class="payment-details mt-4" x-show="selectedPaymentMethod">
                                        <!-- Bank Transfer Details -->
                                        <div x-show="selectedPaymentMethod === 'bank_transfer'">
                                            <div class="alert alert-info">
                                                <h6 class="alert-heading">Bank Transfer Instructions:</h6>
                                                <p class="mb-2">Please transfer the exact amount to the following account:</p>
                                                <ul class="mb-0">
                                                    <li><strong>Bank:</strong> Bank Central Asia (BCA)</li>
                                                    <li><strong>Account Number:</strong> 1234567890</li>
                                                    <li><strong>Account Name:</strong> PT TokoSaya Indonesia</li>
                                                    <li><strong>Amount:</strong> <span x-text="formatCurrency(totalAmount)"></span></li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- E-Wallet Details -->
                                        <div x-show="['gopay', 'ovo', 'dana'].includes(selectedPaymentMethod)">
                                            <div class="alert alert-info">
                                                <h6 class="alert-heading">E-Wallet Payment:</h6>
                                                <p class="mb-0">You will be redirected to complete the payment after placing your order.</p>
                                            </div>
                                        </div>

                                        <!-- Credit Card Form -->
                                        <div x-show="selectedPaymentMethod === 'credit_card'">
                                            <div class="credit-card-form mt-3">
                                                <div class="row">
                                                    <div class="col-md-8 mb-3">
                                                        <label for="card_number" class="form-label">Card Number *</label>
                                                        <input type="text"
                                                               class="form-control"
                                                               id="card_number"
                                                               x-model="payment.card_number"
                                                               placeholder="1234 5678 9012 3456"
                                                               maxlength="19"
                                                               @input="formatCardNumber">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="cvv" class="form-label">CVV *</label>
                                                        <input type="text"
                                                               class="form-control"
                                                               id="cvv"
                                                               x-model="payment.cvv"
                                                               placeholder="123"
                                                               maxlength="4">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-8 mb-3">
                                                        <label for="card_name" class="form-label">Cardholder Name *</label>
                                                        <input type="text"
                                                               class="form-control"
                                                               id="card_name"
                                                               x-model="payment.card_name"
                                                               placeholder="John Doe">
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <label for="expiry" class="form-label">Expiry Date *</label>
                                                        <input type="text"
                                                               class="form-control"
                                                               id="expiry"
                                                               x-model="payment.expiry"
                                                               placeholder="MM/YY"
                                                               maxlength="5"
                                                               @input="formatExpiry">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Coupon Code -->
                                    <div class="coupon-section mt-4">
                                        <div class="d-flex">
                                            <input type="text"
                                                   class="form-control me-2"
                                                   placeholder="Enter coupon code"
                                                   x-model="couponCode">
                                            <button type="button"
                                                    class="btn btn-outline-primary"
                                                    @click="applyCoupon"
                                                    :disabled="loading.coupon">
                                                <span x-show="!loading.coupon">Apply</span>
                                                <span x-show="loading.coupon">
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                </span>
                                            </button>
                                        </div>
                                        <div class="coupon-success mt-2" x-show="appliedCoupon">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Coupon applied: <strong x-text="appliedCoupon.code"></strong>
                                                - <span x-text="formatCurrency(appliedCoupon.discount)"></span> discount
                                                <button type="button" class="btn-close float-end" @click="removeCoupon"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-lg"
                                                @click="goToStep(1)">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Shipping
                                        </button>
                                        <button type="button"
                                                class="btn btn-primary btn-lg"
                                                @click="goToStep(3)"
                                                :disabled="!selectedPaymentMethod">
                                            Review Order
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Order Review -->
                        <div class="checkout-step" x-show="step === 3" x-transition>
                            <div class="card shadow-sm">
                                <div class="card-header">
                                    <h4 class="mb-0">
                                        <i class="fas fa-clipboard-check text-primary me-2"></i>
                                        Review Your Order
                                    </h4>
                                </div>
                                <div class="card-body">
                                    <!-- Order Summary -->
                                    <div class="order-review">
                                        <!-- Shipping Info Review -->
                                        <div class="review-section mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="fw-bold">Shipping Address</h6>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        @click="goToStep(1)">
                                                    Edit
                                                </button>
                                            </div>
                                            <div class="shipping-summary">
                                                <p class="mb-1" x-text="shipping.recipient_name"></p>
                                                <p class="mb-1" x-text="shipping.phone"></p>
                                                <p class="mb-1" x-text="shipping.address_line1"></p>
                                                <p class="mb-1" x-text="shipping.address_line2" x-show="shipping.address_line2"></p>
                                                <p class="mb-0" x-text="`${shipping.city}, ${shipping.state} ${shipping.postal_code}`"></p>
                                            </div>
                                        </div>

                                        <!-- Payment Info Review -->
                                        <div class="review-section mb-4">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="fw-bold">Payment Method</h6>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary"
                                                        @click="goToStep(2)">
                                                    Edit
                                                </button>
                                            </div>
                                            <div class="payment-summary">
                                                <p class="mb-0" x-text="getPaymentMethodName(selectedPaymentMethod)"></p>
                                            </div>
                                        </div>

                                        <!-- Terms and Conditions -->
                                        <div class="terms-section">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="terms_agreement"
                                                       x-model="agreeToTerms"
                                                       required>
                                                <label class="form-check-label" for="terms_agreement">
                                                    I agree to the <a href="{{ route('terms') }}" target="_blank">Terms of Service</a>
                                                    and <a href="{{ route('privacy') }}" target="_blank">Privacy Policy</a>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-between">
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-lg"
                                                @click="goToStep(2)">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Back to Payment
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success btn-lg"
                                                :disabled="!agreeToTerms || loading.checkout">
                                            <span x-show="!loading.checkout">
                                                <i class="fas fa-lock me-2"></i>
                                                Place Order
                                            </span>
                                            <span x-show="loading.checkout">
                                                <i class="fas fa-spinner fa-spin me-2"></i>
                                                Processing...
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Order Summary Sidebar -->
            <div class="col-lg-4">
                <div class="checkout-sidebar sticky-top">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <!-- Cart Items -->
                            <div class="order-items">
                                @foreach($cartItems as $item)
                                <div class="order-item">
                                    <div class="d-flex">
                                        <div class="item-image me-3">
                                            <img src="{{ $item->product->primary_image }}"
                                                 alt="{{ $item->product->name }}"
                                                 class="img-thumbnail">
                                        </div>
                                        <div class="item-details flex-grow-1">
                                            <h6 class="item-name">{{ $item->product->name }}</h6>
                                            @if($item->variant)
                                                <p class="item-variant text-muted">{{ $item->variant->variant_name }}: {{ $item->variant->variant_value }}</p>
                                            @endif
                                            <div class="d-flex justify-content-between">
                                                <span class="item-quantity">Qty: {{ $item->quantity }}</span>
                                                <span class="item-price fw-bold">{{ format_currency($item->total_price_cents) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Price Breakdown -->
                            <div class="price-breakdown mt-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span x-text="formatCurrency(subtotal)"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping</span>
                                    <span x-text="shippingCost > 0 ? formatCurrency(shippingCost) : 'FREE'"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2" x-show="paymentFee > 0">
                                    <span>Payment Fee</span>
                                    <span x-text="formatCurrency(paymentFee)"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2" x-show="discountAmount > 0">
                                    <span class="text-success">Discount</span>
                                    <span class="text-success" x-text="`-${formatCurrency(discountAmount)}`"></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax</span>
                                    <span x-text="formatCurrency(taxAmount)"></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold h5">
                                    <span>Total</span>
                                    <span x-text="formatCurrency(totalAmount)"></span>
                                </div>
                            </div>

                            <!-- Security Notice -->
                            <div class="security-notice mt-3">
                                <div class="alert alert-light">
                                    <i class="fas fa-shield-alt text-success me-2"></i>
                                    <small>Your payment information is secured with 256-bit SSL encryption</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Help Section -->
                    <div class="card shadow-sm mt-3">
                        <div class="card-body text-center">
                            <h6>Need Help?</h6>
                            <p class="text-muted small">Our customer service is available 24/7</p>
                            <div class="help-buttons">
                                <a href="https://wa.me/6281234567890" class="btn btn-outline-success btn-sm me-2">
                                    <i class="fab fa-whatsapp me-1"></i>WhatsApp
                                </a>
                                <a href="tel:+621234567890" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-phone me-1"></i>Call
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" x-show="loading.checkout" x-transition>
    <div class="loading-content">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-3">Processing your order...</p>
    </div>
</div>
@endsection

@push('styles')
<style>
.checkout-progress {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 0;
}

.progress-steps {
    max-width: 600px;
    margin: 0 auto;
}

.step {
    text-align: center;
    color: rgba(255, 255, 255, 0.6);
    position: relative;
}

.step.active {
    color: white;
}

.step.completed {
    color: #28a745;
}

.step-number {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.5rem;
    font-weight: bold;
    transition: all 0.3s ease;
}

.step.active .step-number {
    background: white;
    color: #667eea;
}

.step.completed .step-number {
    background: #28a745;
    color: white;
}

.step-label {
    font-size: 0.875rem;
    font-weight: 500;
}

.address-card {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.address-card:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
}

.address-card.selected {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

.address-label {
    margin-bottom: 0.5rem;
}

.address-details {
    font-size: 0.875rem;
    color: #6c757d;
}

.shipping-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.shipping-option:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
}

.shipping-option.selected {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

.payment-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.payment-option:hover {
    border-color: #0d6efd;
    box-shadow: 0 2px 8px rgba(13, 110, 253, 0.15);
}

.payment-option.selected {
    border-color: #0d6efd;
    background-color: #f8f9ff;
}

.payment-logo-img {
    height: 40px;
    width: auto;
}

.order-item {
    padding: 1rem 0;
    border-bottom: 1px solid #e9ecef;
}

.order-item:last-child {
    border-bottom: none;
}

.item-image img {
    width: 60px;
    height: 60px;
    object-fit: cover;
}

.checkout-sidebar {
    top: 2rem;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.loading-content {
    text-align: center;
    color: white;
}

.security-notice .alert {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
}

@media (max-width: 768px) {
    .checkout-progress {
        padding: 1rem 0;
    }

    .step-number {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }

    .step-label {
        font-size: 0.75rem;
    }

    .checkout-sidebar {
        position: static;
        margin-top: 2rem;
    }

    .order-item .item-image img {
        width: 50px;
        height: 50px;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('checkoutData', () => ({
        step: 1,
        selectedAddress: null,
        showNewAddressForm: {{ auth()->check() && auth()->user()->addresses->count() === 0 ? 'true' : 'false' }},
        hasSavedAddresses: {{ auth()->check() && auth()->user()->addresses->count() > 0 ? 'true' : 'false' }},
        selectedShippingMethod: null,
        selectedPaymentMethod: null,
        shippingMethods: [],
        showShippingMethods: false,
        agreeToTerms: false,
        saveAddress: false,
        couponCode: '',
        appliedCoupon: null,

        // Form data
        shipping: {
            recipient_name: '{{ old("recipient_name", auth()->user()->first_name ?? "") }}',
            phone: '{{ old("phone", auth()->user()->phone ?? "") }}',
            address_line1: '',
            address_line2: '',
            city: '',
            state: '',
            postal_code: ''
        },

        payment: {
            card_number: '',
            cvv: '',
            card_name: '',
            expiry: ''
        },

        // Calculations
        subtotal: {{ $subtotal }},
        shippingCost: 0,
        paymentFee: 0,
        discountAmount: 0,
        taxAmount: 0,

        // Loading states
        loading: {
            checkout: false,
            shipping: false,
            coupon: false
        },

        errors: {},

        init() {
            this.calculateTax();
            this.loadShippingMethods();
        },

        get totalAmount() {
            return this.subtotal + this.shippingCost + this.paymentFee + this.taxAmount - this.discountAmount;
        },

        get canProceedToPayment() {
            if (this.selectedAddress || this.showNewAddressForm) {
                if (this.showNewAddressForm) {
                    return this.shipping.recipient_name &&
                           this.shipping.phone &&
                           this.shipping.address_line1 &&
                           this.shipping.city &&
                           this.shipping.state &&
                           this.shipping.postal_code &&
                           this.selectedShippingMethod;
                }
                return this.selectedShippingMethod;
            }
            return false;
        },

        selectAddress(addressId) {
            this.selectedAddress = addressId;
            this.showNewAddressForm = false;

            // Load address data
            fetch(`/api/addresses/${addressId}`)
                .then(response => response.json())
                .then(data => {
                    this.shipping = {
                        recipient_name: data.recipient_name,
                        phone: data.phone,
                        address_line1: data.address_line1,
                        address_line2: data.address_line2 || '',
                        city: data.city,
                        state: data.state,
                        postal_code: data.postal_code
                    };
                    this.loadShippingMethods();
                });
        },

        async loadShippingMethods() {
            if (!this.shipping.city || !this.shipping.state) return;

            this.loading.shipping = true;
            try {
                const response = await fetch('/api/shipping-methods', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        destination: {
                            city: this.shipping.city,
                            state: this.shipping.state,
                            postal_code: this.shipping.postal_code
                        },
                        weight: {{ $totalWeight }},
                        value: this.subtotal
                    })
                });

                const data = await response.json();
                this.shippingMethods = data.methods;
                this.showShippingMethods = true;
            } catch (error) {
                console.error('Error loading shipping methods:', error);
            } finally {
                this.loading.shipping = false;
            }
        },

        selectShippingMethod(methodId, cost) {
            this.selectedShippingMethod = methodId;
            this.shippingCost = cost;
        },

        selectPaymentMethod(methodCode, fee) {
            this.selectedPaymentMethod = methodCode;
            this.paymentFee = fee;
        },

        getPaymentMethodName(code) {
            const methods = {
                'bank_transfer': 'Bank Transfer',
                'credit_card': 'Credit Card',
                'gopay': 'GoPay',
                'ovo': 'OVO',
                'dana': 'DANA'
            };
            return methods[code] || code;
        },

        goToStep(stepNumber) {
            this.step = stepNumber;
            window.scrollTo(0, 0);
        },

        async applyCoupon() {
            if (!this.couponCode.trim()) return;

            this.loading.coupon = true;
            try {
                const response = await fetch('/api/coupons/apply', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        code: this.couponCode,
                        order_total: this.subtotal
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.appliedCoupon = data.coupon;
                    this.discountAmount = data.discount_amount;
                    this.couponCode = '';

                    // Show success message
                    this.showAlert('Coupon applied successfully!', 'success');
                } else {
                    this.showAlert(data.message || 'Invalid coupon code', 'error');
                }
            } catch (error) {
                this.showAlert('Error applying coupon', 'error');
            } finally {
                this.loading.coupon = false;
            }
        },

        removeCoupon() {
            this.appliedCoupon = null;
            this.discountAmount = 0;
        },

        calculateTax() {
            // Simple tax calculation (11% for Indonesia)
            this.taxAmount = Math.round(this.subtotal * 0.11);
        },

        formatCurrency(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(amount / 100);
        },

        formatCardNumber() {
            // Format card number with spaces
            this.payment.card_number = this.payment.card_number
                .replace(/\s/g, '')
                .replace(/(.{4})/g, '$1 ')
                .trim();
        },

        formatExpiry() {
            // Format expiry date MM/YY
            let value = this.payment.expiry.replace(/\D/g, '');
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2, 4);
            }
            this.payment.expiry = value;
        },

        async processCheckout() {
            this.loading.checkout = true;
            this.errors = {};

            try {
                const orderData = {
                    shipping_address: this.shipping,
                    shipping_method_id: this.selectedShippingMethod,
                    payment_method: this.selectedPaymentMethod,
                    payment_details: this.payment,
                    coupon_code: this.appliedCoupon?.code,
                    save_address: this.saveAddress,
                    terms_agreed: this.agreeToTerms
                };

                const response = await fetch('{{ route("checkout.process") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(orderData)
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to success page or payment gateway
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = `/orders/${data.order_id}/success`;
                    }
                } else {
                    this.errors = data.errors || {};
                    this.showAlert(data.message || 'Please check your information and try again', 'error');

                    // Go back to step with errors
                    if (Object.keys(this.errors).some(key => key.startsWith('shipping'))) {
                        this.goToStep(1);
                    } else if (Object.keys(this.errors).some(key => key.startsWith('payment'))) {
                        this.goToStep(2);
                    }
                }
            } catch (error) {
                console.error('Checkout error:', error);
                this.showAlert('An error occurred. Please try again.', 'error');
            } finally {
                this.loading.checkout = false;
            }
        },

        showAlert(message, type) {
            // Create and show alert notification
            const alert = document.createElement('div');
            alert.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
            alert.style.cssText = 'top: 20px; right: 20px; z-index: 10000; min-width: 300px;';
            alert.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(alert);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 5000);
        }
    }))
});
</script>
@endpush
