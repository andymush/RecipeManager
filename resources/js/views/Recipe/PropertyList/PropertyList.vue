<template>
  <form @submit.prevent="$emit('update')">
    <ul>
      <property-list-item
        v-if="!$env.DISABLE_COOKBOOK && recipe.cookbook"
        :label="$t('Cookbook')"
        :value="recipe.cookbook | name | hyphenate"
      >
        <rm-select
          v-model="cookbook_id"
          size="is-small"
          :options="[{ id: null, name: 'Öffentlich' }, ...cookbooks]"
        />
      </property-list-item>

      <property-list-item
        :label="$t('Author')"
        :value="recipe.author.name"
        :disable-editing="true"
      />

      <property-list-item :label="$t('Category')" :value="recipe.category | name | hyphenate">
        <rm-select v-model="category_id" size="is-small" :options="categories" required />
      </property-list-item>

      <property-list-item :label="$t('Yield amount')">
        <rm-numberinput
          v-model="yield_amount"
          size="is-small"
          :min="0"
          :max="999"
          :step="1"
          :placeholder="$t('Enter yield amount...')"
        />
        <template v-slot:fallback>
          <rm-numberinput
            v-model="yieldAmountMultiplier"
            size="is-small"
            :min="0"
            :max="999"
            :step="1"
            :placeholder="$t('Enter yield amount...')"
          />
        </template>
      </property-list-item>

      <property-list-item :label="$t('Complexity')" :value="recipe.complexity | name | hyphenate">
        <rm-select v-model="complexity" size="is-small" :options="complexities" required />
      </property-list-item>

      <property-list-item :label="$t('Preparation time')" :value="recipe.preparation_time">
        <rm-timepicker v-model="preparation_time" size="is-small"></rm-timepicker>
        <template v-slot:fallback>
          <span>{{ preparation_time | humanReadablePreparationTime | hyphenate }}</span>
        </template>
      </property-list-item>

      <property-list-item :label="$t('Tags')">
        <rm-multiselect
          v-model="tag_ids"
          size="is-small"
          :placeholder="$t('Choose tags...')"
          :options="tags"
        />
        <template v-if="recipe.tags && recipe.tags.length" v-slot:fallback>
          <router-link
            :key="tag.id"
            v-for="tag in recipe.tags"
            class="tag is-success"
            tag="a"
            :to="{ name: 'home', query: { 'search[tag]': tag.slug } }"
          >{{ tag.name }}</router-link>
        </template>
        <template v-else v-slot:fallback>-</template>
      </property-list-item>
    </ul>

    <rm-submit-button v-if="editmode.editing">
      {{ $t('Save') }}
      <template v-slot:buttons>
        <b-button
          @click="$store.commit('recipe/editmode/edit', { editing: false })"
          type="is-danger"
        >{{ $t('Cancel') }}</b-button>
      </template>
    </rm-submit-button>
  </form>
</template>

<script>
import { mapState } from "vuex";
import PropertyListItem from "./PropertyListItem";

export default {
  components: { PropertyListItem },
  data() {
    return {
      yieldAmountMultiplier: null
    };
  },
  computed: {
    ...mapState({
      tags: state => state.tags.data,
      categories: state => state.categories.data,
      cookbooks: state => state.cookbooks.data.data,
      complexities: state => state.recipe.complexities,
      recipe: state => state.recipe.data,
      form: state => state.recipe.form.data,
      editmode: state => state.recipe.editmode.data
    }),
    cookbook_id: {
      get() {
        return this.form.cookbook_id;
      },
      set(value) {
        this.updateFormProperty("cookbook_id", value);
      }
    },
    category_id: {
      get() {
        return this.form.category_id;
      },
      set(value) {
        this.updateFormProperty("category_id", value);
      }
    },
    complexity: {
      get() {
        return this.form.complexity;
      },
      set(value) {
        this.updateFormProperty("complexity", value);
      }
    },
    yield_amount: {
      get() {
        return this.form.yield_amount;
      },
      set(value) {
        this.yieldAmountMultiplier = this.recipe.yield_amount;
        this.updateFormProperty("yield_amount", value);
      }
    },
    tag_ids: {
      get() {
        return this.form.tags;
      },
      set(value) {
        this.updateFormProperty("tags", value);
      }
    },
    preparation_time: {
      get() {
        return this.form.preparation_time;
      },
      set(value) {
        this.updateFormProperty("preparation_time", value);
      }
    }
  },
  watch: {
    yieldAmountMultiplier(value, oldValue) {
      if (oldValue != null) {
        if (this.recipe.yield_amount != value) {
          let yield_amount = value;
          this.$router.push({ query: { ...this.$route.query, yield_amount } });
        } else {
          let query = Object.assign({}, this.$route.query);
          delete query.yield_amount;
          this.$router.push({ query });
        }
      }

      this.$emit("multiply", (1 / this.recipe.yield_amount) * value);
    }
  },
  mounted() {
    setTimeout(() => {
      let yieldAmount = this.$route.query.yield_amount;
      if (!yieldAmount) {
        yieldAmount = this.recipe.yield_amount;
      }
      this.yieldAmountMultiplier = yieldAmount;
    }, 1000);
  },
  methods: {
    updateFormProperty(property, value) {
      this.$store.dispatch("recipe/form/update", { property, value });
    }
  }
};
</script>

<style lang="scss" scoped>
ul {
  margin-bottom: 5px;

  li {
    display: flex;
    align-items: baseline;
    min-height: 30px;

    > div {
      margin: 1px;
    }

    > a {
      margin-right: 3px;
    }
  }
}
</style>