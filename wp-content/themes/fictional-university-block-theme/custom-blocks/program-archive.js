wp.blocks.registerBlockType("fictional-university/program-archive", {
  title: "Fictional University Program Archive",
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
    "Program Archive Placeholder"
  );
}
