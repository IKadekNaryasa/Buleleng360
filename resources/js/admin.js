document.querySelectorAll("[data-search-select]").forEach((select) => {
    const input = select.querySelector("[data-search-input]");
    const valueInput = select.querySelector("[data-search-value]");
    const options = [...select.querySelectorAll("[data-search-option]")];
    const optionsPanel = select.querySelector("[data-search-options]");

    const filterOptions = () => {
        const query = input.value.trim().toLowerCase();
        let visibleCount = 0;

        options.forEach((option) => {
            const matches = option.dataset.label.toLowerCase().includes(query);
            option.hidden = !matches;
            visibleCount += matches ? 1 : 0;
        });

        return visibleCount;
    };

    const openOptions = () => {
        const query = input.value.trim();
        const visibleCount = filterOptions();

        optionsPanel.hidden = query.length === 0 || visibleCount === 0;
    };

    optionsPanel.hidden = true;
    input.addEventListener("focus", openOptions);
    input.addEventListener("input", () => {
        valueInput.value = "";
        openOptions();
    });

    options.forEach((option) => {
        option.addEventListener("click", () => {
            input.value = option.dataset.label;
            valueInput.value = option.dataset.value;
            optionsPanel.hidden = true;
        });
    });

    document.addEventListener("click", (event) => {
        if (!select.contains(event.target)) {
            optionsPanel.hidden = true;
        }
    });
});
