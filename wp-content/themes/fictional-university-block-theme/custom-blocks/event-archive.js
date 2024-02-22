wp.blocks.registerBlockType("fictional-university/event-archive", {
  title: "Fictional University Event Archive",
  save: SaveComponent,
  edit: EditComponent,
});

function SaveComponent() {
  return null;
}

function EditComponent() {
  return wp.element.createElement(
    "div",
    { className: "placeholder-block" },
    "Event Archive Placeholder"
  );
}
