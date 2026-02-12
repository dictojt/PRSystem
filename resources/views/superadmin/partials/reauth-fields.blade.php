<div class="reauth-block">
    <p>Confirm your identity: request an OTP to your email and enter it below.</p>
    <p class="otp-sent" style="display: none;">OTP sent to your email. Enter it below.</p>
    <p class="otp-error alert-danger" style="display: none;"></p>
    <button type="button" class="send-otp-btn">Send OTP to my email</button>
    <label>
        Enter OTP
        <input type="text" name="otp" placeholder="6-digit code" maxlength="6" pattern="[0-9]*" inputmode="numeric" required>
    </label>
    @error('otp')
        <div class="alert-danger" style="margin-top: 8px;">{{ $message }}</div>
    @enderror
</div>
