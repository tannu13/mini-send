<template>
  <div>
    <section class="row mb-3">
      <div class="col">
        <h2>Email Activities</h2>
      </div>
    </section>
    <section class="row" id="filter_box">
      <div class="col">
        <div class="card mb-4">
          <div class="card-content px-3 py-2 my-1">
            <div class="row collapse-icon left accordion-icon-rotate">
              <div class="col">
                <div class="position-relative">
                  <h5>
                    <a
                      data-toggle="collapse"
                      href="#filterBlock"
                      role="button"
                      aria-expanded="false"
                      aria-controls="filterBlock"
                      class="d-block"
                    >
                      Search
                      <i
                        class="fas fa-chevron-down"
                        style="font-size: 0.7rem"
                      ></i>
                    </a>
                  </h5>
                </div>
              </div>
            </div>
            <div class="collapse multi-collapse pb-2" id="filterBlock">
              <div class="row mt-1">
                <div class="col-lg-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Search by sender..."
                    v-model="searchBySender"
                  />
                </div>
                <div class="col-lg-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Search by recipient..."
                    v-model="searchByRecipient"
                  />
                </div>
                <div class="col-lg-3">
                  <input
                    type="text"
                    class="form-control"
                    placeholder="Search by subject..."
                    v-model="searchBySubject"
                  />
                </div>
                <div class="col-lg-3">
                  <select
                    class="form-control"
                    v-model="searchByStatus"
                    @change="filterListings"
                  >
                    <option value="" selected>Search by status</option>
                    <option value="posted">Posted</option>
                    <option value="sent">Sent</option>
                    <option value="failed">Failed</option>
                  </select>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <section class="row">
      <div class="col">
        <span class="item-count">Items: {{ filteredEmails.length }} </span>
      </div>
    </section>
    <section class="row">
      <div class="col">
        <div class="card p-4">
          <template v-if="filteredEmails.length > 0">
            <ul class="email-list">
              <template v-for="(item, index) in filteredEmails">
                <li class="mb-1 py-3 border-bottom" :key="index">
                  <div class="row">
                    <div class="col-4">
                      <strong>To: {{ item.recipient }}</strong>
                      <br />
                      <small class="from-address"
                        >From: {{ item.sender }}</small
                      >
                      <i
                        class="fas fa-paperclip text-secondary"
                        v-if="item.attachments"
                        :title="
                          'Has ' +
                          JSON.parse(item.attachments).length +
                          ' attachment(s)'
                        "
                      ></i>
                    </div>
                    <div class="col-2">
                      <email-status-badge
                        :status="item.status"
                      ></email-status-badge>
                    </div>
                    <div class="col-4 text-truncate" :title="item.subject">
                      {{ item.subject }}
                    </div>
                    <div class="col-2 actions">
                      <template v-if="isDeleting && isDeleting == item.id">
                        <a
                          href="#"
                          class="text-secondary"
                          @click.prevent="isDeleting = false"
                          >Cancel</a
                        >
                        <button
                          type="button"
                          class="btn btn-danger btn-sm ml-1"
                          @click="deleteActivity(item.id)"
                        >
                          Confirm
                        </button>
                      </template>
                      <template v-else>
                        <router-link
                          :to="{ name: 'one-email', params: { id: item.id } }"
                          ><i class="fas fa-eye"></i
                        ></router-link>
                        <a href="#" @click.prevent="isDeleting = item.id"
                          ><i class="far fa-trash-alt"></i
                        ></a>
                      </template>
                    </div>
                  </div>
                </li>
              </template>
            </ul>
          </template>
          <template v-else>
            <div>You have no activity</div>
          </template>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import emailStatusBadge from "../../utils/emailStatusBadge.vue";
export default {
  components: { emailStatusBadge },
  mounted() {
    axios.get("/api/email-activities").then(({ data }) => {
      this.emails = data;
      this.filterListings();
    });
  },
  data: function () {
    return {
      emails: [],
      filteredEmails: [],
      searchBySender: "",
      searchByRecipient: "",
      searchBySubject: "",
      searchByStatus: "",
      isDeleting: false,
    };
  },
  watch: {
    searchBySender: function () {
      this.debounceSearchListings();
    },
    searchByRecipient: function () {
      this.debounceSearchListings();
    },
    searchBySubject: function () {
      this.debounceSearchListings();
    },
  },
  created() {
    this.debounceSearchListings = _.debounce(this.filterListings, 500);
  },
  methods: {
    filterListings: function () {
      let data = this.emails;
      const that = this;
      _.forEach(["Sender", "Recipient", "Subject"], function (criteria) {
        const re = new RegExp(".*" + that["searchBy" + criteria] + ".*", "i");
        data = _.filter(data, function (email) {
          let statusChk = true;
          if (
            that.searchByStatus != "" &&
            that.searchByStatus != email.status
          ) {
            statusChk = false;
          }
          return re.test(email[criteria.toLowerCase()]) && statusChk;
        });
      });
      this.filteredEmails = _.orderBy(data, "id", "desc");
    },
    deleteActivity: function (id) {
      let that = this;
      axios.delete(`/api/email-activities/${id}`).then(() => {
        const index = _.findIndex(that.emails, { id: id });
        that.emails.splice(index, 1);
        that.filterListings();
      });
    },
  },
};
</script>

<style scoped lang="scss">
ul.email-list {
  padding-left: 0;
  li {
    list-style: none;
  }

  .from-address {
    font-style: italic;
    color: #707070;
  }
  .actions {
    text-align: right;
    padding-right: 30px;
    i {
      cursor: pointer;
      margin-right: 4px;
    }
  }
}
.item-count {
  float: right;
  color: #6c757d;
  font-size: 0.7rem;
  font-weight: bold;
}
</style>