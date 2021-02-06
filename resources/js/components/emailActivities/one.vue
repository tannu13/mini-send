
<template>
  <div>
    <section class="row mb-3">
      <div class="col-11">
        <h2>
          Email Activities

          <small
            class="text-secondary text-truncate d-inline-block align-middle mb-1 w-50"
          >
            / {{ email.subject }}
          </small>
        </h2>
      </div>
      <div class="col-1">
        <router-link
          :to="{ name: 'emails' }"
          style="font-size: 1.7rem"
          class="float-right"
        >
          <i class="fas fa-arrow-circle-left"></i>
        </router-link>
      </div>
    </section>
    <section class="row">
      <div class="col">
        <div class="card p-4">
          <template v-if="isDataLoaded">
            <div class="row">
              <div class="col">
                <h5>
                  <span>{{ email.subject }}</span>
                </h5>
              </div>
            </div>
            <div class="row">
              <div class="col-6">
                <span class="mr-2 font-weight-bold">To:</span>
                <span>{{ email.recipient }}</span>
                <br />
                <span class="mr-2 font-weight-bold">From:</span>
                <span>{{ email.sender }}</span>
              </div>
              <div class="col-6">
                <email-status-badge
                  class="float-right"
                  :status="email.status"
                ></email-status-badge>
                <span class="clearfix"></span>
                <span
                  class="float-right text-secondary mt-1"
                  style="font-size: 0.7rem"
                  >{{ formattedDate(email.updated_at) }}</span
                >
              </div>
            </div>
            <ul class="nav nav-tabs mt-4">
              <li class="nav-item">
                <a
                  class="nav-link"
                  :class="{
                    active: activeTab == 'html',
                    disabled: !email.html_content,
                  }"
                  aria-current="page"
                  href="#"
                  @click.prevent="activeTab = 'html'"
                >
                  HTML Content
                </a>
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  @click.prevent="activeTab = 'text'"
                  href="#"
                  :class="{
                    active: activeTab == 'text',
                    disabled: !email.text_content,
                  }"
                  >Text Content</a
                >
              </li>
              <li class="nav-item">
                <a
                  class="nav-link"
                  @click.prevent="activeTab = 'attachments'"
                  href="#"
                  :class="{
                    active: activeTab == 'attachments',
                    disabled: !email.attachments,
                  }"
                >
                  Attachments</a
                >
              </li>
            </ul>
            <div class="row">
              <div class="col">
                <div
                  class="p-3 border border-top-0 browser"
                  v-show="activeTab == 'html'"
                >
                  <iframe
                    id="content-html"
                    frameBorder="0"
                    v-show="!isIframeLoading"
                    >No Content</iframe
                  >
                </div>
                <div
                  class="p-3 border border-top-0"
                  v-show="activeTab == 'text'"
                >
                  <textarea
                    v-model="email.text_content"
                    rows="15"
                    cols="100"
                    disabled
                  ></textarea>
                </div>
                <div
                  class="p-3 border border-top-0"
                  v-show="activeTab == 'attachments'"
                  v-if="email.attachments && email.attachments.length > 0"
                >
                  <span class="font-weight-bold">
                    Total of {{ email.attachments.length }} files uploaded
                  </span>
                  <ul class="list-group w-50 mt-3">
                    <li
                      class="list-group-item"
                      v-for="(item, index) in email.attachments"
                      :key="index"
                    >
                      {{ item.orig_name }}
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <div>Loading email preview...</div>
          </template>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import moment from "moment";
import emailStatusBadge from "../../utils/emailStatusBadge.vue";
export default {
  components: { emailStatusBadge },
  mounted() {
    axios
      .get("/api/email-activities/" + this.$route.params.id)
      .then(({ data }) => {
        this.email = data;
        this.email.attachments = JSON.parse(this.email.attachments);
        this.isDataLoaded = true;
        if (this.email.html_content) {
          this.htmlContent = this.email.html_content;
        }
      });
  },
  data: function () {
    return {
      email: {},
      isDataLoaded: false,
      isIframeLoading: false,
      htmlContent: "",
      activeTab: "html",
    };
  },
  watch: {
    "email.html_content": function () {
      this.$nextTick(() => this.htmlUpdated());
    },
  },
  methods: {
    htmlUpdated: function () {
      let iframeElement = document.getElementById("content-html");

      let doc = iframeElement.contentWindow.document;
      doc.open();
      doc.write(this.email.html_content || "No content");
      doc.close();
    },
    formattedDate: function (dateStr) {
      return moment(dateStr).format("YYYY-MM-DD H:m");
    },
  },
};
</script>

<style scoped lang="scss">
.browser {
  height: 620px;
}
#content-html {
  width: 100%;
  height: 100%;
  border: 0;
}
.nav-link.disabled {
  text-decoration: line-through;
}
</style>