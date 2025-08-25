<template>
  <q-layout view="hHh lpR fFf">
    <q-header class="custom-header">
      <q-toolbar class="q-px-md q-gutter-x-md q-gutter-y-sm flex-wrap justify-between items-center">
        <img
          src="~assets/icons/Newlogo.svg"
          alt="Butalent"
          style="height: 50px"
          class="cursor-pointer"
        />

        <div class="q-gutter-sm q-mt-sm q-mt-none-md">
          <q-btn
            flat
            label="Log in"
            to="/auth"
            class="custom-link-btn"
          />
          <q-btn
            unelevated
            label="Sign up"
            to="/auth"
            class="text-white custom-signup-btn"
          />
        </div>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <router-view />
    </q-page-container>
  </q-layout>
</template>


<style scoped>
.custom-header {
  width: 100%;
  background-color: #ffffff;
  border: 1px solid #B3B3B3;
  border-radius: 40px;
  padding: 20px;
  font-family: 'Inter', sans-serif;
  box-shadow: none !important;
}

.custom-link-btn {
  border-radius: 15px;
  border: 1px solid #3F419A;
  color: #3F419A;
  min-width: 100px;
}

.custom-link-btn:hover {
  background-color: #ffffff;
  transition: 0.2s ease;
}

.custom-signup-btn {
  border-radius: 15px;
  background-color: #3F419A;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.25);
  min-width: 100px;
}

.custom-signup-btn:hover {
  transition: 0.2s ease;
}

/* Можно дополнительно подстроить кнопку на маленьких экранах */
@media (max-width: 600px) {
  .custom-link-btn,
  .custom-signup-btn {
    width: 100%;
  }
}

</style>
