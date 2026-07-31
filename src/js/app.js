import { initHeroAnimation } from "./hero-animation.js";

initHeroAnimation();

/**
 * Members archive: toggle the Experts / Organizations panels.
 */
const tabMenu = document.querySelector(".members-tab-menu");
if (tabMenu) {
  tabMenu.addEventListener("click", (e) => {
    const item = e.target.closest(".members-tab-item");
    if (!item) {
      return;
    }
    tabMenu.querySelectorAll(".members-tab-item").forEach((i) => {
      i.classList.remove("active");
    });
    item.classList.add("active");

    const showExperts = document.querySelector(".members-experts-btn")?.classList.contains("active");
    document.querySelector(".members-experts-list")?.classList.toggle("hidden", !showExperts);
    document.querySelector(".members-organizations-list")?.classList.toggle("hidden", !!showExperts);
  });
}

/**
 * Campaign detail page: open/close the video popup.
 */
document.querySelectorAll(".campaign-vid-thumbnail").forEach((thumb) => {
  thumb.addEventListener("click", () => {
    const match = thumb
      .getAttribute("data-src")
      ?.match(/(?:youtube(?:-nocookie)?\.com\/(?:watch\?v=|embed\/|v\/|shorts\/)|youtu\.be\/)([\w-]{11})/);
    if (!match) {
      return;
    }
    const modal = thumb.parentElement.querySelector(".video-page");
    if (!modal) {
      return;
    }
    document.body.style.overflowY = "hidden";
    modal.classList.add("show");
    const iframe = modal.querySelector("iframe");
    if (iframe) {
      iframe.setAttribute("src", `https://www.youtube.com/embed/${match[1]}`);
    }
  });
});
document.querySelectorAll(".video-close").forEach((close) => {
  close.addEventListener("click", () => {
    const modal = close.closest(".video-page");
    document.body.style.overflowY = "auto";
    modal?.classList.remove("show");
    const iframe = modal?.querySelector("iframe");
    if (iframe) {
      iframe.setAttribute("src", "");
    }
  });
});

/**
 * Responsive navbar: close it when clicking outside.
 */
document.addEventListener("mouseup", (e) => {
  const container = document.querySelector(".wp-block-navigation__responsive-container");
  if (container && !container.contains(e.target)) {
    document.querySelector(".wp-block-navigation__responsive-container-close")?.click();
  }
});
