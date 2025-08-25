<template>
  <div class="user-profile">
    <!-- Profile Header -->
    <div class="profile-header q-pa-lg">
      <div class="row items-center q-gutter-md">
        <!-- Avatar -->
        <div class="avatar-section">
          <q-avatar
            size="80px"
            color="primary"
            text-color="white"
            class="q-mb-sm"
          >
            <q-img v-if="user?.avatar" :src="user.avatar" alt="User Avatar" />
            <span v-else class="text-h4">{{ userInitials }}</span>
          </q-avatar>

          <q-btn
            flat
            round
            color="primary"
            icon="edit"
            size="sm"
            @click="editAvatar = true"
          />
        </div>

        <!-- User Info -->
        <div class="user-info">
          <h3 class="text-h5 text-weight-bold q-mb-xs">
            {{ userDisplayName }}
          </h3>
          <p class="text-body1 text-grey-7 q-mb-xs">@{{ user?.username }}</p>
          <p class="text-body2 text-grey-6">{{ user?.email }}</p>
          <p v-if="user?.phone" class="text-body2 text-grey-6">
            {{ user.phone }}
          </p>
        </div>

        <!-- Actions -->
        <div class="profile-actions q-ml-auto">
          <q-btn
            color="primary"
            label="Edit profile"
            @click="editProfile = true"
          />
        </div>
      </div>
    </div>

    <!-- Profile Stats -->
    <div class="profile-stats q-pa-lg">
      <div class="row q-gutter-md">
        <div class="col-12 col-sm-4">
          <q-card class="stat-card text-center">
            <q-card-section>
              <div class="text-h4 text-primary">{{ stats.posts }}</div>
              <div class="text-body2 text-grey-7">Posts</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-4">
          <q-card class="stat-card text-center">
            <q-card-section>
              <div class="text-h4 text-primary">{{ stats.followers }}</div>
              <div class="text-body2 text-grey-7">Followers</div>
            </q-card-section>
          </q-card>
        </div>
        <div class="col-12 col-sm-4">
          <q-card class="stat-card text-center">
            <q-card-section>
              <div class="text-h4 text-primary">{{ stats.following }}</div>
              <div class="text-body2 text-grey-7">Following</div>
            </q-card-section>
          </q-card>
        </div>
      </div>
    </div>

    <!-- Profile Actions -->
    <div class="profile-actions-section q-pa-lg">
      <q-card>
        <q-card-section>
          <h4 class="text-h6 q-mb-md">Actions</h4>
          <div class="row q-gutter-md">
            <q-btn
              color="primary"
              icon="lock"
              label="Change password"
              @click="changePasswordDialog = true"
            />
            <q-btn
              color="secondary"
              icon="notifications"
              label="Notification settings"
              @click="notificationsSettings = true"
            />
            <q-btn
              color="negative"
              icon="logout"
              label="Sign out"
              @click="confirmSignOut"
            />
          </div>
        </q-card-section>
      </q-card>
    </div>

    <!-- Edit Profile Dialog -->
    <q-dialog v-model="editProfile" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
          <div class="text-h6">Edit profile</div>
        </q-card-section>

        <q-card-section>
          <q-form @submit="updateProfile" class="q-gutter-md">
            <q-input v-model="editForm.firstName" label="First name" filled dense />
            <q-input v-model="editForm.lastName" label="Last name" filled dense />
            <q-input v-model="editForm.phone" label="Phone" filled dense />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            label="Save"
            color="primary"
            @click="updateProfile"
            :loading="isLoading"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Change Password Dialog -->
    <q-dialog v-model="changePasswordDialog" persistent>
      <q-card style="min-width: 400px">
        <q-card-section>
              <div class="text-h6">Change password</div>
        </q-card-section>

        <q-card-section>
          <q-form @submit="changePassword" class="q-gutter-md">
            <q-input
              v-model="passwordForm.currentPassword"
              label="Current password"
              type="password"
              filled
              dense
              :rules="[(val) => !!val || 'Enter current password']"
            />
            <q-input
              v-model="passwordForm.newPassword"
              label="New password"
              type="password"
              filled
              dense
              :rules="[(val) => !!val || 'Enter new password']"
            />
            <q-input
              v-model="passwordForm.confirmPassword"
              label="Confirm new password"
              type="password"
              filled
              dense
              :rules="[
                (val) => !!val || 'Confirm password',
                (val) =>
                  val === passwordForm.newPassword || 'Passwords do not match',
              ]"
            />
          </q-form>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn
            label="Change"
            color="primary"
            @click="changePassword"
            :loading="isLoading"
          />
        </q-card-actions>
      </q-card>
    </q-dialog>

    <!-- Confirm Sign Out Dialog -->
    <q-dialog v-model="confirmSignOutDialog">
      <q-card>
        <q-card-section>
          <div class="text-h6">Confirm sign out</div>
        </q-card-section>

        <q-card-section>
          <p>Are you sure you want to sign out?</p>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn flat label="Cancel" color="primary" v-close-popup />
          <q-btn label="Sign out" color="negative" @click="signOut" />
        </q-card-actions>
      </q-card>
    </q-dialog>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive } from "vue";
import { useQuasar } from "quasar";
import { useAuth } from "../composables/useAuth";
// import type { User } from "../components/models";

const $q = useQuasar();
const {
  user,
  isLoading,
  userDisplayName,
  userInitials,
  updateProfile: updateUserProfile,
  changePassword: changeUserPassword,
  signOut: signOutUser,
} = useAuth();

// Dialog states
const editProfile = ref(false);
const changePasswordDialog = ref(false);
const confirmSignOutDialog = ref(false);
const editAvatar = ref(false);
const notificationsSettings = ref(false);

// Form data
const editForm = reactive({
  firstName: user.value?.firstName || "",
  lastName: user.value?.lastName || "",
  phone: user.value?.phone || "",
});

const passwordForm = reactive({
  currentPassword: "",
  newPassword: "",
  confirmPassword: "",
});

// Mock stats (замените на реальные данные)
const stats = reactive({
  posts: 42,
  followers: 128,
  following: 64,
});

// Methods
const updateProfile = async () => {
  try {
    await updateUserProfile(editForm);
    $q.notify({
      type: "positive",
      message: "Profile successfully updated",
      position: "top",
    });
    editProfile.value = false;
  } catch (error) {
    console.error("Profile update error:", error);
  }
};

const changePassword = async () => {
  try {
    await changeUserPassword(
      passwordForm.currentPassword,
      passwordForm.newPassword
    );

    $q.notify({
      type: "positive",
      message: "Password successfully changed",
      position: "top",
    });

    // Reset form
    passwordForm.currentPassword = "";
    passwordForm.newPassword = "";
    passwordForm.confirmPassword = "";

    changePasswordDialog.value = false;
  } catch (error) {
    console.error("Password change error:", error);
  }
};

const confirmSignOut = () => {
  confirmSignOutDialog.value = true;
};

const signOut = async () => {
  try {
    await signOutUser();
    confirmSignOutDialog.value = false;
  } catch (error) {
    console.error("Sign out error:", error);
  }
};

// Watch for user changes to update edit form
import { watch } from "vue";
watch(
  user,
  (newUser) => {
    if (newUser) {
      editForm.firstName = newUser.firstName || "";
      editForm.lastName = newUser.lastName || "";
      editForm.phone = newUser.phone || "";
    }
  },
  { immediate: true }
);
</script>

<style scoped>
.user-profile {
  max-width: 800px;
  margin: 0 auto;
}

.profile-header {
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 16px;
  margin-bottom: 24px;
}

.avatar-section {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.avatar-section .q-btn {
  position: absolute;
  bottom: 0;
  right: 0;
  background: white;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.user-info h3 {
  color: #2c3e50;
  margin: 0;
}

.profile-stats {
  margin-bottom: 24px;
}

.stat-card {
  transition: transform 0.2s ease;
}

.stat-card:hover {
  transform: translateY(-2px);
}

.profile-actions-section {
  margin-bottom: 24px;
}

.profile-actions-section .q-btn {
  margin-bottom: 8px;
}

@media (max-width: 600px) {
  .profile-header .row {
    flex-direction: column;
    text-align: center;
  }

  .profile-actions {
    margin-top: 16px;
    text-align: center;
  }

  .profile-actions .q-btn {
    width: 100%;
    margin-bottom: 8px;
  }
}
</style>
