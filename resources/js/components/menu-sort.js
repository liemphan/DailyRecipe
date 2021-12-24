import Sortable from "sortablejs";

class MenuSort {

    constructor(elem) {
        this.elem = elem;
        this.input = document.getElementById('books-input');
        this.menuBooksList = elem.querySelector('[menu-sort-assigned-books]');

        this.initSortable();
        this.setupListeners();
    }

    initSortable() {
        const scrollBoxes = this.elem.querySelectorAll('.scroll-box');
        for (let scrollBox of scrollBoxes) {
            new Sortable(scrollBox, {
                group: 'menu-books',
                ghostClass: 'primary-background-light',
                animation: 150,
                onSort: this.onChange.bind(this),
            });
        }
    }

    setupListeners() {
        this.elem.addEventListener('click', event => {
            const sortItem = event.target.closest('.scroll-box-item:not(.instruction)');
            if (sortItem) {
                event.preventDefault();
                this.sortItemClick(sortItem);
            }
        });
    }

    /**
     * Called when a sort item is clicked.
     * @param {Element} sortItem
     */
    sortItemClick(sortItem) {
        const lists = this.elem.querySelectorAll('.scroll-box');
        const newList = Array.from(lists).filter(list => sortItem.parentElement !== list);
        if (newList.length > 0) {
            newList[0].appendChild(sortItem);
        }
        this.onChange();
    }

    onChange() {
        const menuBookElems = Array.from(this.menuBooksList.querySelectorAll('[data-id]'));
        this.input.value = menuBookElems.map(elem => elem.getAttribute('data-id')).join(',');
    }

}

export default MenuSort;