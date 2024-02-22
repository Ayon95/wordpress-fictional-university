import "./index.scss";
import { registerBlockType } from "@wordpress/blocks";
import {
  TextControl,
  Flex,
  FlexBlock,
  FlexItem,
  Button,
  Icon,
  PanelBody,
  PanelRow,
  ColorPicker,
} from "@wordpress/components";
import {
  InspectorControls,
  BlockControls,
  AlignmentToolbar,
} from "@wordpress/block-editor";

registerBlockType("aypa-plugin/are-you-paying-attention", {
  title: "Are You Paying Attention?",
  category: "common",
  attributes: {
    question: { type: "string" },
    answers: { type: "array", default: [""] },
    correctAnswer: { type: "string", default: null },
    bgColor: { type: "string", default: "#ebebeb" },
    textAlignment: { enum: ["left", "center", "right"], default: "left" },
  },
  description: "Give your audience a chance to prove their comprehension",
  example: {
    attributes: {
      question: "In which province is Montreal located?",
      answers: ["Ontario", "British Columbia", "Quebec"],
      correctAnswer: "Quebec",
      bgColor: "#dae3f0",
      textAlignment: "left",
    },
  },
  edit: Edit,
  save: Save,
});

function Edit({ attributes, setAttributes }) {
  function updateAnswer(newAnswer, index) {
    const updatedAnswers = [...attributes.answers];
    updatedAnswers[index] = newAnswer;
    setAttributes({ answers: updatedAnswers });
  }

  function addAnswerSlot() {
    setAttributes({ answers: [...attributes.answers, ""] });
  }

  function deleteAnswer(answerToDelete) {
    const updatedAnswers = attributes.answers.filter(
      (answer) => answer !== answerToDelete
    );
    setAttributes({ answers: updatedAnswers });

    if (answerToDelete === attributes.correctAnswer) {
      setAttributes({ correctAnswer: null });
    }
  }

  return (
    <div
      className="aypa-edit-block"
      style={{ backgroundColor: attributes.bgColor }}
    >
      <BlockControls>
        <AlignmentToolbar
          value={attributes.textAlignment}
          onChange={(alignment) => setAttributes({ textAlignment: alignment })}
        />
      </BlockControls>
      <InspectorControls>
        <PanelBody title="Background Color">
          <PanelRow>
            <ColorPicker
              color={attributes.bgColor}
              onChangeComplete={(color) =>
                setAttributes({ bgColor: color.hex })
              }
            />
          </PanelRow>
        </PanelBody>
      </InspectorControls>
      <TextControl
        value={attributes.question}
        onChange={(value) => setAttributes({ question: value })}
        label="Question"
        style={{ fontSize: "20px" }}
      />
      <p style={{ fontSize: "14px", margin: "20px 0 8px 0" }}>Answers</p>
      {attributes.answers.map((answer, i) => (
        <Flex>
          <FlexBlock>
            <TextControl
              label={`answer-${i}`}
              hideLabelFromVision={true}
              value={answer}
              onChange={(answer) => updateAnswer(answer, i)}
              autoFocus={answer === ""}
            />
          </FlexBlock>
          <FlexItem>
            <Button
              className="aypa-edit-block__mark-as-correct"
              size="compact"
              onClick={() => setAttributes({ correctAnswer: answer })}
            >
              <Icon
                icon={
                  answer === attributes.correctAnswer
                    ? "star-filled"
                    : "star-empty"
                }
              />
            </Button>
          </FlexItem>
          <FlexItem>
            <Button
              className="aypa-edit-block__delete-btn"
              size="small"
              onClick={() => deleteAnswer(answer)}
            >
              Delete
            </Button>
          </FlexItem>
        </Flex>
      ))}
      <Button variant="primary" onClick={addAnswerSlot}>
        Add another answer
      </Button>
    </div>
  );
}

function Save() {
  return null;
}
