document.addEventListener('alpine:init', () => {
    Alpine.directive('auto-scroll', (el, {}, { cleanup }) => {
        let lastHeight = el.scrollHeight;
        const scrollableDiv = el;

        const detectChange = () => {
            const currentHeight = scrollableDiv.scrollHeight;
            if (lastHeight !== currentHeight) {
                scrollableDiv.scrollTo({
                    top: currentHeight,
                    behavior: 'smooth'
                });
                lastHeight = currentHeight;
            }
        };

        const intervalId = setInterval(detectChange, 200);

        cleanup(() => {
            clearInterval(intervalId);
        });
    });
});
