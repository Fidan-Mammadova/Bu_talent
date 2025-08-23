<template>
  <q-page class="auth-page flex flex-center q-pa-md">
    <!-- Background Pattern -->
    <div class="background-pattern"></div>

    <!-- Main Auth Card -->
    <q-card class="auth-card shadow-24">
      <!-- Logo Section -->
      <div class="logo-section text-center q-pa-lg">
        <div class="logo-container q-mb-md">
          <div class="logo-icon">b</div>
        </div>
        <h3 class="text-h4 text-weight-bold text-primary q-mb-xs">Butalent</h3>
        <p class="text-body1 text-grey-7">Find your talent</p>
      </div>

      <!-- Tabs Header -->
      <div class="tab-header">
        <div class="tab-container">
          <div
            class="tab-item"
            :class="{ active: tab === 'signup' }"
            @click="switchTab('signup')"
          >
            <q-icon name="person_add" class="q-mr-sm" />
            Sign up
          </div>
          <div
            class="tab-item"
            :class="{ active: tab === 'signin' }"
            @click="switchTab('signin')"
          >
            <q-icon name="login" class="q-mr-sm" />
            Sign in
          </div>
        </div>
      </div>

      <!-- Tab Content -->
      <q-tab-panels v-model="tab" animated class="tab-content">
        <q-tab-panel name="signup" class="q-pa-none">
          <SignUpForm @success="onSignUpSuccess" />
        </q-tab-panel>
        <q-tab-panel name="signin" class="q-pa-none">
          <SignInForm @success="onSignInSuccess" />
        </q-tab-panel>
      </q-tab-panels>

      <!-- Footer -->
      <div class="auth-footer text-center q-pa-md">
        <p class="text-caption text-grey-6">
          By continuing, you agree to our
          <a href="#" class="text-primary">terms</a> and
          <a href="#" class="text-primary">privacy policy</a>
        </p>
      </div>
    </q-card>

    <!-- Success Dialog -->
    <q-dialog v-model="showSuccessDialog" persistent>
      <q-card class="success-dialog">
        <q-card-section class="text-center">
          <q-icon
            name="check_circle"
            size="4rem"
            color="positive"
            class="q-mb-md"
          />
          <h5 class="text-h5 q-mb-sm">{{ successMessage }}</h5>
          <p class="text-body1 text-grey-7">{{ successDescription }}</p>
        </q-card-section>
        <q-card-actions align="center">
          <q-btn
            label="Continue"
            color="primary"
            @click="handleSuccessContinue"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import SignUpForm from "components/SignUpForm.vue";
import SignInForm from "components/SignInForm.vue";

const router = useRouter();

// State
const tab = ref<"signup" | "signin">("signup");
const showSuccessDialog = ref(false);
const successMessage = ref("");
const successDescription = ref("");

// Methods
const switchTab = (newTab: "signup" | "signin") => {
  tab.value = newTab;
};

const onSignUpSuccess = () => {
  successMessage.value = "Account created!";
  successDescription.value =
    "Your account has been successfully created. You can now sign in.";
  showSuccessDialog.value = true;
  tab.value = "signin";
};

const onSignInSuccess = () => {
  successMessage.value = "Welcome!";
  successDescription.value = "You have successfully signed in.";
  showSuccessDialog.value = true;
};

const handleSuccessContinue = () => {
  showSuccessDialog.value = false;
  if (tab.value === "signin") {
    // Redirect to home page after successful login
    void router.push("/");
  }
};

// Check URL parameters for initial tab
onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const initialTab = urlParams.get("tab");
  if (initialTab === "signin" || initialTab === "signup") {
    tab.value = initialTab;
  }
});
</script>

<style scoped>
.auth-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  overflow: hidden;
}

.background-pattern {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-image:
    radial-gradient(
      circle at 25% 25%,
      rgba(255, 255, 255, 0.1) 0%,
      transparent 50%
    ),
    radial-gradient(
      circle at 75% 75%,
      rgba(255, 255, 255, 0.1) 0%,
      transparent 50%
    );
  pointer-events: none;
}

.auth-card {
  width: 100%;
  max-width: 450px;
  border-radius: 24px;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.logo-section {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.logo-container {
  display: flex;
  justify-content: center;
  align-items: center;
}

.logo-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  font-weight: bold;
  color: white;
  box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
}

.logo-section h3 {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.tab-header {
  background: #f8f9fa;
  border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.tab-container {
  display: flex;
  background: white;
  margin: 16px;
  border-radius: 12px;
  padding: 4px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.tab-item {
  flex: 1;
  padding: 12px 16px;
  text-align: center;
  cursor: pointer;
  border-radius: 8px;
  transition: all 0.3s ease;
  font-weight: 500;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
}

.tab-item:hover {
  color: #495057;
  background: rgba(102, 126, 234, 0.1);
}

.tab-item.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.tab-content {
  background: transparent;
}

.auth-footer {
  background: #f8f9fa;
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.auth-footer a {
  text-decoration: none;
  font-weight: 500;
}

.auth-footer a:hover {
  text-decoration: underline;
}

.success-dialog {
  min-width: 300px;
  border-radius: 16px;
}

/* Responsive Design */
@media (max-width: 600px) {
  .auth-card {
    margin: 16px;
    border-radius: 16px;
  }

  .logo-section {
    padding: 20px;
  }

  .logo-section h3 {
    font-size: 1.5rem;
  }

  .tab-item {
    padding: 10px 12px;
    font-size: 0.9rem;
  }
}

/* Animation for tab switching */
.q-tab-panels {
  transition: all 0.3s ease;
}

/* Custom scrollbar for forms */
.tab-content ::v-deep(.q-tab-panel) {
  max-height: 70vh;
  overflow-y: auto;
}

.tab-content ::v-deep(.q-tab-panel)::-webkit-scrollbar {
  width: 6px;
}

.tab-content ::v-deep(.q-tab-panel)::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.tab-content ::v-deep(.q-tab-panel)::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.tab-content ::v-deep(.q-tab-panel)::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>
