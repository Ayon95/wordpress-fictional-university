import { registerBlockType } from "@wordpress/blocks";
import { ToolbarGroup, ToolbarButton } from "@wordpress/components";
import { RichText, BlockControls } from "@wordpress/block-editor";

const headingTags = {
  large: "h1",
  medium: "h2",
  small: "h3",
};

registerBlockType("fictional-university/generic-heading", {
  title: "Generic Heading",
  attributes: {
    text: { type: "string" },
    size: { enum: ["small", "medium", "large"], default: "large" },
  },
  edit: EditComponent,
  save: SaveComponent,
});

function EditComponent({ attributes, setAttributes }) {
  function handleChangeText(currentValue) {
    setAttributes({ text: currentValue });
  }

  return (
    <>
      <BlockControls>
        <ToolbarGroup>
          <ToolbarButton
            isPressed={attributes.size === "large"}
            onClick={() => setAttributes({ size: "large" })}
          >
            Large
          </ToolbarButton>
          <ToolbarButton
            isPressed={attributes.size === "medium"}
            onClick={() => setAttributes({ size: "medium" })}
          >
            Medium
          </ToolbarButton>
          <ToolbarButton
            isPressed={attributes.size === "small"}
            onClick={() => setAttributes({ size: "small" })}
          >
            Small
          </ToolbarButton>
        </ToolbarGroup>
      </BlockControls>
      <RichText
        tagName={headingTags["large"]}
        allowedFormats={["core/bold", "core/italic"]}
        className={`headline headline--${attributes.size}`}
        value={attributes.text}
        onChange={handleChangeText}
      />
    </>
  );
}

function SaveComponent({ attributes }) {
  return (
    <RichText.Content
      tagName={headingTags[attributes.size]}
      className={`headline headline--${attributes.size}`}
      value={attributes.text}
    />
  );
}
