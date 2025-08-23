<template>
  <q-page class="profile-page q-pa-md">
    <!-- Loading State -->
    <div v-if="isLoading" class="flex flex-center" style="min-height: 400px">
      <q-spinner-dots size="50px" color="primary" />
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="error-state text-center q-pa-lg">
      <q-icon name="error" size="4rem" color="negative" class="q-mb-md" />
      <h4 class="text-h5 text-negative q-mb-sm">An error occurred</h4>
      <p class="text-body1 text-grey-7 q-mb-md">{{ error }}</p>
      <q-btn color="primary" label="Try again" @click="initializeAuth" />
    </div>

    <!-- Profile Content -->
    <div v-else-if="isAuthenticated && user" class="profile-content">
      <!-- Page Header -->
      <div class="page-header q-mb-lg">
        <div class="row items-center justify-between">
          <div>
            <h1 class="text-h4 text-weight-bold text-primary q-mb-xs">
              Profile
            </h1>
            <p class="text-body1 text-grey-7">Manage your account</p>
          </div>
          <q-btn
            color="primary"
            icon="settings"
            label="Settings"
            @click="showSettings = true"
          />
        </div>
      </div>

      <!-- Profile Component -->
      <UserProfile />
    </div>

    <!-- Not Authenticated State -->
    <div v-else class="not-authenticated text-center q-pa-lg">
      <q-icon name="person_off" size="4rem" color="grey-5" class="q-mb-md" />
      <h4 class="text-h5 text-grey-7 q-mb-sm">You are not authorized</h4>
      <p class="text-body1 text-grey-6 q-mb-md">Sign in to view your profile</p>
      <div class="row justify-center q-gutter-md">
        <q-btn color="primary" label="Sign in" @click="goToAuth" />
        <q-btn
          outline
          color="primary"
          label="Sign up"
          @click="goToAuthSignUp"
        />
      </div>
    </div>

    <!-- Settings Dialog -->
    <q-dialog v-model="showSettings" persistent>
      <q-card style="min-width: 500px">
        <q-card-section>
          <div class="text-h6">Account settings</div>
        </q-card-section>

        <q-card-section>
          <q-list>
            <q-item
              clickable
              v-ripple
              @click="showNotificationsSettings = true"
            >
              <q-item-section avatar>
                <q-icon name="notifications" color="primary" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Notifications</q-item-label>
                <q-item-label caption
                  >Notification and email settings</q-item-label
                >
              </q-item-section>
              <q-item-section side>
                <q-icon name="chevron_right" />
              </q-item-section>
            </q-item>

            <q-item clickable v-ripple @click="showPrivacySettings = true">
              <q-item-section avatar>
                <q-icon name="security" color="primary" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Privacy</q-item-label>
                <q-item-label caption
                  >Privacy and security settings</q-item-label
                >
              </q-item-section>
              <q-item-section side>
                <q-icon name="chevron_right" />
              </q-item-section>
            </q-item>

            <q-item clickable v-ripple @click="showDataExport = true">
              <q-item-section avatar>
                <q-icon name="download" color="primary" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Data export</q-item-label>
                <q-item-label caption>Download your data</q-item-label>
              </q-item-section>
              <q-item-section side>
                <q-icon name="chevron_right" />
              </q-item-section>
            </q-item>

            <q-item clickable v-ripple @click="showDeleteAccount = true">
              <q-item-section avatar>
                <q-icon name="delete_forever" color="negative" />
              </q-item-section>
              <q-item-section>
                <q-item-label class="text-negative"
                  >Delete account</q-item-label
                >
                <q-item-label caption
                  >Permanently delete your account and all data</q-item-label
                >
              </q-item-section>
              <q-item-section side>
                <q-icon name="chevron_right" />
              </q-item-section>
            </q-item>
          </q-list>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Close" color="primary" v-close-popup />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Notifications Settings Dialog -->
    <q-dialog v-model="showNotificationsSettings" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Notification settings</div>
        </q-card-section>

        <q-card-section>
          <div class="q-gutter-md">
            <q-toggle
              v-model="notificationSettings.email"
              label="Email notifications"
              color="primary"
            />
            <q-toggle
              v-model="notificationSettings.push"
              label="Push notifications"
              color="primary"
            />
            <q-toggle
              v-model="notificationSettings.sms"
              label="SMS notifications"
              color="primary"
            />
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            label="Save"
            color="primary"
            @click="saveNotificationSettings"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Privacy Settings Dialog -->
    <q-dialog v-model="showPrivacySettings" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Privacy settings</div>
        </q-card-section>

        <q-card-section>
          <div class="q-gutter-md">
            <q-toggle
              v-model="privacySettings.profilePublic"
              label="Public profile"
              color="primary"
            />
            <q-toggle
              v-model="privacySettings.showEmail"
              label="Show email"
              color="primary"
            />
            <q-toggle
              v-model="privacySettings.showPhone"
              label="Show phone"
              color="primary"
            />
          </div>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn label="Save" color="primary" @click="savePrivacySettings" />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Data Export Dialog -->
    <q-dialog v-model="showDataExport" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Data export</div>
        </q-card-section>

        <q-card-section>
          <p class="text-body1 q-mb-md">
            We will prepare an archive with your data. This may take a few
            minutes.
          </p>
          <q-select
            v-model="exportFormat"
            :options="exportFormats"
            label="Export format"
            filled
            dense
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            label="Export"
            color="primary"
            @click="exportData"
            :loading="exporting"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Delete Account Dialog -->
    <q-dialog v-model="showDeleteAccount" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6 text-negative">Delete account</div>
        </q-card-section>

        <q-card-section>
          <p class="text-body1 q-mb-md">
            This action cannot be undone. All your data will be permanently
            deleted.
          </p>
          <q-input
            v-model="deleteConfirmation"
            label="Enter 'DELETE' to confirm"
            filled
            dense
            :rules="[
              (val) =>
                val === 'DELETE' || 'Enter the exact word for confirmation',
            ]"
          />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            label="Delete account"
            color="negative"
            @click="deleteAccount"
            :disable="deleteConfirmation !== 'DELETE'"
            :loading="deleting"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </q-page>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { useRouter } from "vue-router";
import { useQuasar } from "quasar";
import { useAuth } from "../composables/useAuth";
import UserProfile from "../components/UserProfile.vue";

const router = useRouter();
const $q = useQuasar();

const { user, isAuthenticated, isLoading, error, initializeAuth } = useAuth();

// Dialog states
const showSettings = ref(false);
const showNotificationsSettings = ref(false);
const showPrivacySettings = ref(false);
const showDataExport = ref(false);
const showDeleteAccount = ref(false);

// Settings data
const notificationSettings = reactive({
  email: true,
  push: true,
  sms: false,
});

const privacySettings = reactive({
  profilePublic: true,
  showEmail: false,
  showPhone: false,
});

const exportFormat = ref("json");
const exportFormats = [
  { label: "JSON", value: "json" },
  { label: "CSV", value: "csv" },
  { label: "XML", value: "xml" },
];

const deleteConfirmation = ref("");
const exporting = ref(false);
const deleting = ref(false);

// Methods
const goToAuth = () => {
  void router.push("/auth");
};

const goToAuthSignUp = () => {
  void router.push("/auth?tab=signup");
};

const saveNotificationSettings = () => {
  // Здесь будет сохранение настроек уведомлений
  $q.notify({
    type: "positive",
    message: "Notification settings saved",
    position: "top",
  });
  showNotificationsSettings.value = false;
};

const savePrivacySettings = () => {
  // Здесь будет сохранение настроек конфиденциальности
  $q.notify({
    type: "positive",
    message: "Privacy settings saved",
    position: "top",
  });
  showPrivacySettings.value = false;
};

const exportData = async () => {
  exporting.value = true;

  try {
    // Имитация экспорта данных
    await new Promise((resolve) => setTimeout(resolve, 2000));

    $q.notify({
      type: "positive",
      message: "Data successfully exported",
      position: "top",
    });

    showDataExport.value = false;
  } catch {
    $q.notify({
      type: "negative",
      message: "Error exporting data",
      position: "top",
    });
  } finally {
    exporting.value = false;
  }
};

const deleteAccount = async () => {
  deleting.value = true;

  try {
    // Здесь будет удаление аккаунта
    await new Promise((resolve) => setTimeout(resolve, 2000));

    $q.notify({
      type: "positive",
      message: "Account successfully deleted",
      position: "top",
    });

    showDeleteAccount.value = false;
    void router.push("/auth");
  } catch {
    $q.notify({
      type: "negative",
      message: "Error deleting account",
      position: "top",
    });
  } finally {
    deleting.value = false;
  }
};
</script>

<style scoped>
.profile-page {
  min-height: 100vh;
  background: #f5f7fa;
}

.page-header {
  background: white;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.error-state,
.not-authenticated {
  background: white;
  border-radius: 16px;
  padding: 48px 24px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.profile-content {
  max-width: 1200px;
  margin: 0 auto;
}

@media (max-width: 600px) {
  .page-header .row {
    flex-direction: column;
    text-align: center;
  }

  .page-header .q-btn {
    margin-top: 16px;
    width: 100%;
  }
}
</style>
