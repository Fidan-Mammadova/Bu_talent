<template>
  <div class="signin-form">
    <div class="form-header q-mb-lg">
      <h4 class="text-h5 text-weight-bold text-center q-mb-xs">Welcome!</h4>
      <p class="text-body2 text-grey-6 text-center">Sign in to your account</p>
    </div>

    <q-form @submit="submitLogin" class="q-gutter-md">
      <!-- Email/Phone Input -->
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
        type="password"
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

      <!-- Remember Me & Forgot Password -->
      <div class="row justify-between items-center q-mt-sm">
        <q-checkbox
          v-model="form.rememberMe"
          label="Remember me"
          color="primary"
        />
        <q-btn
          flat
          no-caps
          label="Forgot password?"
          color="primary"
          class="text-caption"
          @click="forgotPassword"
        />
      </div>

      <!-- Submit Button -->
      <q-btn
        label="Sign in"
        type="submit"
        color="primary"
        class="full-width q-mt-md"
        size="lg"
        :loading="loading"
        :disable="!isFormValid"
      />

      <!-- Social Login -->
      <div class="social-login q-mt-lg">
        <div class="text-caption text-grey-6 text-center q-mb-md">
          Or sign in with social media
        </div>

        <div class="row justify-center q-gutter-sm">
          <q-btn
            flat
            round
            color="red"
            icon="fab fa-google"
            size="lg"
            @click="socialLogin('google')"
          />
          <q-btn
            flat
            round
            color="blue"
            icon="fab fa-facebook"
            size="lg"
            @click="socialLogin('facebook')"
          />
          <q-btn
            flat
            round
            color="black"
            icon="fab fa-apple"
            size="lg"
            @click="socialLogin('apple')"
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
import type { LoginForm } from "./models";
import { validateEmail } from "./models";

const $q = useQuasar();
const { signIn, error: authError } = useAuth();

// Form data
const form = reactive<LoginForm>({
  emailOrPhone: "",
  password: "",
  rememberMe: false,
});

// UI state
const loading = ref(false);
const showPassword = ref(false);

// Validation errors
const errors = reactive({
  emailOrPhone: "",
  password: "",
});

// Computed properties
const isFormValid = computed(() => {
  return (
    form.emailOrPhone.trim() !== "" &&
    form.password.trim() !== "" &&
    !errors.emailOrPhone &&
    !errors.password
  );
});

// Validation methods
const validateField = (field: keyof typeof errors) => {
  errors[field] = "";

  switch (field) {
    case "emailOrPhone":
      if (!form.emailOrPhone.trim()) {
        errors.emailOrPhone = "Email or phone number is required";
      } else if (
        !validateEmail(form.emailOrPhone) &&
        !/^\+?[\d\s\-()]+$/.test(form.emailOrPhone)
      ) {
        errors.emailOrPhone = "Enter a valid email or phone number";
      }
      break;

    case "password":
      if (!form.password.trim()) {
        errors.password = "Password is required";
      } else if (form.password.length < 6) {
        errors.password = "Password must contain at least 6 characters";
      }
      break;
  }
};

const validateForm = (): boolean => {
  validateField("emailOrPhone");
  validateField("password");

  return !errors.emailOrPhone && !errors.password;
};

// Form submission
const submitLogin = async () => {
  if (!validateForm()) {
    return;
  }

  loading.value = true;

  try {
    await signIn(form);

    $q.notify({
      type: "positive",
      message: "Successfully signed in!",
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

// Social login
const socialLogin = (provider: string) => {
  console.log(`Social login with ${provider}`);
  $q.notify({
    type: "info",
    message: `Sign in with ${provider} in development`,
    position: "top",
  });
};

// Forgot password
const forgotPassword = () => {
  console.log("Forgot password clicked");
  $q.notify({
    type: "info",
    message: "Forgot password function in development",
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
.signin-form {
  padding: 20px;
}

.form-header h4 {
  color: #132043;
  margin: 0;
}

.form-header p {
  margin: 0;
}

.social-login {
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
