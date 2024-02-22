import { registerBlockType } from "@wordpress/blocks";
import { link } from "@wordpress/icons";
import {
  ToolbarGroup,
  ToolbarButton,
  Button,
  Popover,
  PanelBody,
  PanelRow,
  ColorPalette,
} from "@wordpress/components";
import {
  RichText,
  BlockControls,
  InspectorControls,
  __experimentalLinkControl as LinkControl,
  getColorObjectByColorValue,
} from "@wordpress/block-editor";
import { useState } from "@wordpress/element";

import colors from "../includes/colors";

registerBlockType("fictional-university/generic-button", {
  title: "Generic Button",
  attributes: {
    text: { type: "string" },
    size: { enum: ["small", "medium", "large"], default: "large" },
    colorName: { enum: colors.map((color) => color.name), default: "blue" },
    linkObject: { type: "object", default: { url: "" } },
  },
  edit: EditComponent,
  save: SaveComponent,
});

function EditComponent(props) {
  const { attributes, setAttributes } = props;
  const [linkPickerIsVisible, setLinkPickerIsVisible] = useState(false);

  const currentColorValue = colors.find(
    (colorObject) => colorObject.name === attributes.colorName
  ).color;

  function handleChangeText(currentValue) {
    setAttributes({ text: currentValue });
  }

  function handleClickLinkPickerToggle() {
    setLinkPickerIsVisible((currentValue) => !currentValue);
  }

  function closeLinkPicker() {
    setLinkPickerIsVisible(false);
  }

  function handleChangeLink(newLink) {
    setAttributes({ linkObject: newLink });
  }

  function handleChangeColor(colorCode) {
    const { name } = getColorObjectByColorValue(colors, colorCode);
    setAttributes({ colorName: name });
  }

  return (
    <>
      <BlockControls>
        <ToolbarGroup>
          <ToolbarButton icon={link} onClick={handleClickLinkPickerToggle} />
        </ToolbarGroup>
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
      <InspectorControls>
        <PanelBody title="Color" initialOpen={true}>
          <PanelRow>
            <ColorPalette
              colors={colors}
              value={currentColorValue}
              onChange={handleChangeColor}
              disableCustomColors={true}
              clearable={false}
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
      <RichText
        tagName="a"
        allowedFormats={[]}
        className={`btn btn--${attributes.size} btn--${attributes.colorName}`}
        value={attributes.text}
        onChange={handleChangeText}
      />
      {linkPickerIsVisible && (
        <Popover position="middle center" onFocusOutside={closeLinkPicker}>
          <LinkControl
            settings={[]}
            value={props.attributes.linkObject}
            onChange={handleChangeLink}
          />
          <Button
            variant="primary"
            onClick={closeLinkPicker}
            style={{ display: "block", width: "100%" }}
          >
            Confirm Link
          </Button>
        </Popover>
      )}
    </>
  );
}

function SaveComponent({ attributes }) {
  return (
    <a
      href={attributes.linkObject.url}
      className={`btn btn--${attributes.size} btn--${attributes.colorName}`}
    >
      {attributes.text}
    </a>
  );
}
