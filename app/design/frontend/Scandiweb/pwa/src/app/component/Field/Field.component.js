import React from 'react';
import SourceField from 'SourceComponent/Field/Field.component';
import Input from 'Component/Input';
import './Field.style';

export const TEXT_TYPE = 'text';
export const NUMBER_TYPE = 'number';
export const RADIO_TYPE = 'radio';
export const CHECKBOX_TYPE = 'checkbox';
export const TEXTAREA_TYPE = 'textarea';
export const PASSWORD_TYPE = 'password';
export const SELECT_TYPE = 'select';

const ENTER_KEY_CODE = 13;
const A_KEY_CODE = 65;
const z_KEY_CODE = 122;
const Z_KEY_CODE = 90;
const a_KEY_CODE = 97;


/**
 * Input fields component
 * @class Field
 */
export default class Field extends SourceField {
    renderTypeNumber() {
        const {
            min,
            max
        } = this.props;

        const { value } = this.state;

        return (
            <>
                <Input
                  { ...this.props }
                  type="number"
                  // eslint-disable-next-line react/jsx-no-bind
                  onChange={ e => this.handleChange(e.target.value, false) }
                  onKeyDown={ this.onKeyEnterDown }
                  onBlur={ this.onChange }
                  onClick={ this.onClick }
                  value={ this.props.value }
                />
                <button
                  disabled={ +value === max }
                  onClick={ () => this.handleChange(+value + 1) }
                >
                    <span>+</span>
                </button>
                <button
                  disabled={ +value === min }
                  onClick={ () => this.handleChange(+value - 1) }
                >
                    <span>–</span>
                </button>
            </>
        );
    }
}
