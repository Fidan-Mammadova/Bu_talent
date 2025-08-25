<template>
  <q-page class="bg-white">
    <div class="content-wrapper">
      <div class="row items-start q-col-gutter-xl">
        <div class="col-12 col-md-6">
          <h2 class="main-heading">"Join now and start learning!"</h2>

          <p class="text-body1 text-justify">
            At Butalent, we believe that learning should be accessible, engaging, and rewarding.
            Whether you're looking to advance your career, develop new skills, or explore your
            passions, our platform provides high-quality courses designed by industry experts.
          </p>

          <h6 class="text-h6 q-mt-md">Why Choose Us?</h6>
          <p class="text-body2 butalent-features-list">
            Expert-Led Courses – Learn from professionals with real-world experience.<br />
            Flexible Learning – Study at your own pace, anytime, anywhere.<br />
            Practical Skills– Gain hands-on knowledge that you can apply immediately.<br />
            Certification– Earn certificates to showcase your achievements.
          </p>

          <p class="text-body1 q-mt-lg text-justify">
            Join thousands of learners worldwide and take the next step in your personal and
            professional growth. Start your learning journey today!
          </p>

          <div class="q-mt-xl q-gutter-sm">
            <q-btn label="Start now" to="/auth" class="start-btn" />
            <q-btn flat label="Add your company" to="/company/register" class="link-btn" />
          </div>
        </div>

        <div class="col-12 col-md-6">
          <q-img
            src="../assets/images/Group 3.png"
            contain
            style="border-radius: 16px"
            class="full-width-on-mobile image-responsive-height"
            loading="lazy"
          />
        </div>
      </div>

      <!-- Our courses -->
      <div class="bg-purple-1 q-pa-md q-mt-xl" style="border-radius: 20px">
        <div class="courses-row">
          <div class="text-courses q-mr-md">Our courses:</div>

          <div class="icons-row">
            <router-link
              v-for="course in courses"
              :key="course.name"
              :to="course.link"
              class="course-link"
              :aria-label="`${course.name} course`"
            >
              <q-img
                :src="course.icon"
                :alt="course.name"
                loading="lazy"
                fit="contain"
                ratio="1"
                style="width: 56px; cursor: pointer"
              />
            </router-link>
          </div>
        </div>
      </div>
    </div>
  </q-page>
</template>

<script setup lang="ts">
const courses = [
  { name: 'C++', icon: '../public/icons/cpp.png', link: '/courses/cpp' },
  { name: '1C', icon: '../public/icons/1C.png', link: '/courses/1c' },
  { name: 'Python', icon: '../public/icons/python.png', link: '/courses/python' },
  { name: 'Office', icon: '../public/icons/OFFICE.png', link: '/courses/office' },
  { name: 'HTML5', icon: '../public/icons/HTML.png', link: '/courses/html5' },
  { name: 'SQL', icon: '../public/icons/SQL.png', link: '/courses/sql' },
  { name: 'JavaScript', icon: '../public/icons/js.png', link: '/courses/js' },
  { name: 'Java', icon: '../public/icons/Java.png', link: '/courses/java' },
];
</script>

<style scoped>
.content-wrapper {
  max-width: 1400px; /* Максимальная ширина контента */
  margin-left: auto; /* Центрирует контейнер */
  margin-right: auto; /* Центрирует контейнер */
}

/* Переопределяем q-pa-md на q-page, чтобы не было двойного паддинга */
.q-page {
  padding: 40px 0 !important; /* Добавляем верхний и нижний паддинг к самой странице, но не по бокам */
}

.main-heading {
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-style: normal;
  font-size: 36px;
  line-height: 100%;
  letter-spacing: 0;
  color: #3f419a;
  text-align: left;
}
.text-body1 {
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-style: normal;
  font-size: 16px;
  line-height: 20px;
  letter-spacing: 0%;
  color: #535353;
  word-break: normal;
  overflow-wrap: normal;
  hyphens: manual;
}
.text-h6 {
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-style: normal;
  font-size: 16px;
  line-height: 20px;
  letter-spacing: 0%;
  color: #535353;
}

.text-body2 {
  font-family: 'Inter', sans-serif;
  font-weight: 400;
  font-style: normal;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0%;
  color: #535353;
}

.butalent-features-list {
  text-align: left;
  margin-top: 0;
  margin-bottom: 0;
  padding-bottom: 16px;
}

.text-justify {
  text-align: justify;
}

.start-btn {
  background-color: #3f419a;
  border-radius: 15px;
  color: white;

  font-size: 12px;
  font-weight: 700;
  font-style: normal;
  line-height: 100%;
  letter-spacing: 0%;
  font-family: 'Inter', sans-serif;
  padding: 12px 20px;
  min-height: unset;
}
.link-btn {
  border-radius: 15px;
  color: #3f419a;
  border: 1px solid #3f419a;

  font-size: 12px;
  font-weight: 700;
  font-style: normal;
  line-height: 100%;
  letter-spacing: 0%;
  font-family: 'Inter', sans-serif;
  padding: 12px 20px;
  min-height: unset;
}


.text-courses{
  font-family: 'Inter', sans-serif;
  font-weight: 600;
  font-style: normal;
  font-size: 24px;
  line-height: 100%;
  letter-spacing: 0;
  color: #3f419a;
  text-align: left;
}

.courses-row {
  display: flex;
  align-items: center; /* выравниваем текст и иконки по центру */
  flex-wrap: wrap;     /* если не влезет в строку на маленьком экране — переносится */
  gap: 16px;           /* расстояние между заголовком и первой иконкой */
}

.icons-row {
  display: flex;
  gap: 20px; /* расстояние между иконками */
  flex-wrap: wrap; /* чтобы на мобильных переносились */
}

/* Стили для изображения */
.image-responsive-height {
  max-width: 100%; /* Убедимся, что изображение не выходит за пределы колонки */
  object-fit: contain; /* Сохраняет пропорции, вписывая изображение */
}

@media (max-width: 900px) {
  /* Для экранов меньше md (768px) до 991px */
  .image-responsive-height {
    margin-top: 24px; /* Отступ сверху, когда изображение под текстом на мобильных */
  }
  .main-heading {
    font-size: 28px; /* Пример: уменьшим размер заголовка на мобильных, если он слишком большой */
  }
}
@media (min-width: 900px) {
  /* Для экранов md и выше */
  .image-responsive-height {
    margin-top: 0; /* Убираем верхний отступ на больших экранах, если он не нужен */
  }
}
</style>
