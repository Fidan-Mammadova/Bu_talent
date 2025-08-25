<template>
  <div class="signup-form">
    <div class="form-header q-mb-lg">
      <h4 class="text-h5 text-weight-bold text-center q-mb-xs">
        Create an account
      </h4>
      <p class="text-body2 text-grey-6 text-center">Join our community</p>
    </div>

    <q-form @submit="submitForm" class="q-gutter-md">
      <!-- Username Input -->
      <q-input
        v-model="form.username"
        label="Username"
        filled
        dense
        :error="!!errors.username"
        :error-message="errors.username"
        @blur="validateField('username')"
        class="full-width"
      >
        <template v-slot:prepend>
          <q-icon name="person" />
        </template>
      </q-input>

      <!-- Email or Phone Input -->
      <q-input
        v-model="form.emailOrPhone"
        label="Email or phone number"
        filled
        dense
        :error="!!errors.emailOrPhone"
        :error-message="errors.emailOrPhone"
        @blur="validateField('emailOrPhone')"
        class="full-width"
      >
        <template v-slot:prepend>
          <q-icon name="email" />
        </template>
      </q-input>

      <!-- Password Input -->
      <q-input
        v-model="form.password"
        label="Password"
        :type="showPassword ? 'text' : 'password'"
        filled
        dense
        :error="!!errors.password"
        :error-message="errors.password"
        @blur="validateField('password')"
        class="full-width"
      >
        <template v-slot:prepend>
          <q-icon name="lock" />
        </template>
        <template v-slot:append>
          <q-icon
            :name="showPassword ? 'visibility_off' : 'visibility'"
            class="cursor-pointer"
            @click="showPassword = !showPassword"
          />
        </template>
      </q-input>

      <!-- Password Strength Indicator -->
      <div v-if="form.password" class="password-strength q-mt-xs">
        <div class="text-caption q-mb-xs">Password strength:</div>
        <div class="strength-bars row q-gutter-xs">
          <div
            v-for="i in 4"
            :key="i"
            class="strength-bar"
            :class="getPasswordStrengthClass(i)"
          ></div>
        </div>
        <div
          class="text-caption q-mt-xs"
          :class="getPasswordStrengthTextClass()"
        >
          {{ getPasswordStrengthText() }}
        </div>
      </div>

      <!-- Terms and Privacy -->
      <div class="terms-section q-mt-md">
     
        <div
          v-if="errors.agreeToTerms"
          class="text-negative text-caption q-mt-xs"
        >
          {{ errors.agreeToTerms }}
        </div>
      </div>

      <div class="row q-gutter-sm items-start">
        <q-checkbox
          v-model="form.agreeToPrivacy"
          color="primary"
          @update:model-value="validateField('agreeToPrivacy')"
        />
        <div class="text-body2">
          I accept
          <a href="#" class="text-primary" @click="showPrivacy"
            >privacy policy</a
          >
        </div>
      </div>
      <div
        v-if="errors.agreeToPrivacy"
        class="text-negative text-caption q-mt-xs"
      >
        {{ errors.agreeToPrivacy }}
      </div>

      <!-- Submit Button -->
      <q-btn
        label="Create account"
        type="submit"
        color="primary"
        class="full-width q-mt-lg"
        size="lg"
        :loading="loading"
        :disable="!isFormValid"
      />

      <!-- Social Sign Up -->
      <div class="social-signup q-mt-lg">
        <div class="text-caption text-grey-6 text-center q-mb-md">
          Or sign up with social media
        </div>

        <div class="row justify-center q-gutter-sm">
          <q-btn
            flat
            round
            color="red"
            icon="fab fa-google"
            size="lg"
            @click="socialSignUp('google')"
          />
          <q-btn
            flat
            round
            color="blue"
            icon="fab fa-facebook"
            size="lg"
            @click="socialSignUp('facebook')"
          />
          <q-btn
            flat
            round
            color="black"
            icon="fab fa-apple"
            size="lg"
            @click="socialSignUp('apple')"
          />
        </div>
      </div>
    </q-form>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, reactive } from "vue";
import { useQuasar } from "quasar";
import { useAuth } from "../composables/useAuth";
import type { SignUpForm } from "./models";
import { validateEmail, validatePassword, validateUsername } from "./models";

const $q = useQuasar();
const { signUp, error: authError } = useAuth();

// Form data
const form = reactive<SignUpForm>({
  username: "",
  emailOrPhone: "",
  password: "",
  agreeToTerms: false,
  agreeToPrivacy: false,
});

// UI state
const loading = ref(false);
const showPassword = ref(false);

// Validation errors
const errors = reactive({
  username: "",
  emailOrPhone: "",
  password: "",
  agreeToTerms: "",
  agreeToPrivacy: "",
});

// Computed properties
const isFormValid = computed(() => {
  return (
    form.username.trim() !== "" &&
    form.emailOrPhone.trim() !== "" &&
    form.password.trim() !== "" &&
    form.agreeToTerms &&
    form.agreeToPrivacy &&
    !errors.username &&
    !errors.emailOrPhone &&
    !errors.password
  );
});

// Validation methods
const validateField = (field: keyof typeof errors) => {
  errors[field] = "";

  switch (field) {
    case "username":
      if (!form.username.trim()) {
        errors.username = "Username is required";
      } else {
        const usernameValidation = validateUsername(form.username);
        if (!usernameValidation.isValid) {
          errors.username = usernameValidation.errors[0] || "Invalid username";
        }
      }
      break;

    case "emailOrPhone":
      if (!form.emailOrPhone.trim()) {
        errors.emailOrPhone = "Email or phone number is required";
      } else {
        if (
          !validateEmail(form.emailOrPhone) &&
          !/^\+?[\d\s\-()]+$/.test(form.emailOrPhone)
        ) {
          errors.emailOrPhone = "Enter a valid email or phone number";
        }
      }
      break;

    case "password":
      if (!form.password.trim()) {
        errors.password = "Password is required";
      } else {
        const passwordValidation = validatePassword(form.password);
        if (!passwordValidation.isValid) {
          errors.password = passwordValidation.errors[0] || "Invalid password";
        }
      }
      break;

    case "agreeToTerms":
      if (!form.agreeToTerms) {
        errors.agreeToTerms = "You must accept the terms of use";
      }
      break;

    case "agreeToPrivacy":
      if (!form.agreeToPrivacy) {
        errors.agreeToPrivacy = "You must accept the privacy policy";
      }
      break;
  }
};

const validateForm = (): boolean => {
  validateField("username");
  validateField("emailOrPhone");
  validateField("password");
  validateField("agreeToTerms");
  validateField("agreeToPrivacy");

  return Object.values(errors).every((error) => !error);
};

// Password strength methods
const getPasswordStrength = (): number => {
  if (!form.password) return 0;

  let strength = 0;
  if (form.password.length >= 8) strength++;
  if (/[A-Z]/.test(form.password)) strength++;
  if (/[a-z]/.test(form.password)) strength++;
  if (/\d/.test(form.password)) strength++;
  if (/[!@#$%^&*(),.?":{}|<>]/.test(form.password)) strength++;

  return Math.min(strength, 4);
};

const getPasswordStrengthClass = (index: number): string => {
  const strength = getPasswordStrength();
  if (index <= strength) {
    if (strength <= 1) return "strength-weak";
    if (strength <= 2) return "strength-medium";
    if (strength <= 3) return "strength-good";
    return "strength-strong";
  }
  return "strength-empty";
};

const getPasswordStrengthText = (): string => {
  const strength = getPasswordStrength();
  if (strength <= 1) return "Weak";
  if (strength <= 2) return "Medium";
  if (strength <= 3) return "Good";
  return "Strong";
};

const getPasswordStrengthTextClass = (): string => {
  const strength = getPasswordStrength();
  if (strength <= 1) return "text-negative";
  if (strength <= 2) return "text-orange";
  if (strength <= 3) return "text-warning";
  return "text-positive";
};

// Form submission
const submitForm = async () => {
  if (!validateForm()) {
    return;
  }

  loading.value = true;

  try {
    await signUp(form);

    $q.notify({
      type: "positive",
      message: "Account successfully created!",
      position: "top",
    });

    // Emit success event for parent component
    emit("success");
  } catch {
    // Error is already handled by useAuth composable
  } finally {
    loading.value = false;
  }
};

// Social sign up
const socialSignUp = (provider: string) => {
  console.log(`Social sign up with ${provider}`);
  $q.notify({
    type: "info",
    message: `Sign up with ${provider} in development`,
    position: "top",
  });
};



const showPrivacy = () => {
  console.log("Show privacy clicked");
  $q.notify({
    type: "info",
    message: "Privacy policy in development",
    position: "top",
  });
};

// Emit events
const emit = defineEmits<{
  success: [];
}>();

// Watch for auth errors
import { watch } from "vue";
watch(authError, (newError) => {
  if (newError) {
    $q.notify({
      type: "negative",
      message: newError,
      position: "top",
    });
  }
});
</script>

<style scoped>
.signup-form {
  padding: 20px;
}

.form-header h4 {
  color: #132043;
  margin: 0;
}

.form-header p {
  margin: 0;
}

.password-strength {
  padding: 10px;
  background-color: #f5f5f5;
  border-radius: 8px;
}

.strength-bars {
  justify-content: flex-start;
}

.strength-bar {
  width: 20px;
  height: 4px;
  border-radius: 2px;
  background-color: #e0e0e0;
}

.strength-weak {
  background-color: #f44336;
}

.strength-medium {
  background-color: #ff9800;
}

.strength-good {
  background-color: #ffc107;
}

.strength-strong {
  background-color: #4caf50;
}

.terms-section {
  border-top: 1px solid #e0e0e0;
  padding-top: 15px;
}

.social-signup {
  border-top: 1px solid #e0e0e0;
  padding-top: 20px;
}

.q-btn[color="red"] {
  background-color: #db4437;
}

.q-btn[color="blue"] {
  background-color: #4267b2;
}

.q-btn[color="black"] {
  background-color: #000000;
}

.q-btn[color="blue-7"] {
  background-color: #0088cc;
}
</style>
