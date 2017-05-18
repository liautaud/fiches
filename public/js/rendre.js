var bus = new Vue();

Vue.component('markdown-editor', {
    template:
        '<div class="mdl-textfield mdl-js-textfield" v-show="!hidden">' +
            '<div class="markdown-editor">' +
                '<textarea  class="mdl-textfield__input markdown" :name="name" :disabled="disabled" @input="update">{{ value }}</textarea>' +
                '<div class="markdown-render" v-html="compiledMarkdown"></div>' +
            '</div>' +
        '</div>',
    props: ['initial', 'name', 'disabled', 'hidden'],
    computed: {
        compiledMarkdown: function () {
            if (this.value) {
                return marked(this.value, {sanitize: true});
            } else {
                return '';
            }
        }
    },
    data: function() {
    	return {
    		'value': JSON.parse(this.initial)
    	};
    },
    methods: {
        update: function(e) {
            this.value = e.target.value;
            e.target.style.height = '5px';
            e.target.style.height = (e.target.scrollHeight) + 'px';
        }
    }
});

Vue.component('code-editor', {
    template:
        '<div class="mdl-textfield mdl-js-textfield" v-show="!hidden">' +
            '<div v-show="!hidden">' +
                '<div class="code-editor"></div>' +
                '<input class="code-result" type="hidden" :name="name" :value="value" />' +
            '</div>' +
        '</div>',
    props: ['initial', 'theme', 'lang', 'name', 'disabled', 'hidden', 'numero'],
    data: function () {
        return {
            editor: null,
            value: JSON.parse(this.initial)
        };
    },
	mounted: function () {
        var self = this;

        var theme = this.theme || 'tomorrow';
        var lang = this.lang || 'python';

        var editorNode = this.$el.getElementsByClassName('code-editor')[0];
        var resultNode = this.$el.getElementsByClassName('code-result')[0];
        
        this.editor = ace.edit(editorNode);
        this.editor.setTheme("ace/theme/" + theme);
        this.editor.getSession().setMode("ace/mode/" + lang);
        this.editor.setHighlightActiveLine(false);
        this.editor.setHighlightGutterLine(false);
        this.editor.getSession().on("change", function () {
            self.value = self.editor.getSession().getValue();
        });

		this.editor.getSession().setValue(this.value);
    }
});

Vue.component('file-uploader', {
    template:
        '<div class="mdl-button mdl-button--primary mdl-button--icon mdl-button--file">' +
            '<i class="material-icons" v-if="empty">attach_file</i>' +
            '<i class="material-icons" v-else @click="clear">clear</i>' +
            '<input type="file" :name="name" @change="update" v-show="empty" />' +
        '</div>',
    props: {
        name: String,
        beginEmpty: Boolean,
        empty: {
            type: Boolean,
            default: function () {
                return this.beginEmpty;
            }
        },
        numero: Number
    },
    mounted: function () {
        var self = this;
        bus.$emit('visibility-change:' + self.numero, self.empty);
    },
    methods: {
        update: function(e) {
            this.empty = (e.target.value == '');
            bus.$emit('visibility-change:' + this.numero, this.empty);
        },
        clear: function(e) {
            this.empty = true;
            this.$el.getElementsByTagName('input').value = '';
            bus.$emit('visibility-change:' + this.numero, this.empty);
        }
    }
});

new Vue({
    el: '#fiche-submit'
});
