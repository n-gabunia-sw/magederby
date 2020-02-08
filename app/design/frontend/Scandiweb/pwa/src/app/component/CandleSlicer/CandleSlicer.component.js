import { PureComponent, createRef } from 'react';
import Image from 'Component/Image/Image.component';
import CSS from 'Util/CSS';
import Draggable from 'Component/Draggable';
import './CandleSlicer.style.scss';

export default class CandleSlicer extends PureComponent {
    rootRef = createRef();

    mainRef = createRef();

    secondaryRef = createRef();

    maskRef = createRef();

    dragRef = createRef();

    originalX = 0;

    currentRotation = 0;

    candleData = [
        { name: 'Amaranth', url: '/media/product-images/amaranth.png' },
        { name: 'Baby Blue', url: '/media/product-images/baby-blue.png' },
        { name: 'Desert Sand', url: '/media/product-images/desert-sand.png' },
        { name: 'Orange', url: '/media/product-images/orange.png' },
        { name: 'Orchid', url: '/media/product-images/orchid.png' },
        { name: 'Pear', url: '/media/product-images/Pear.png' },
        { name: 'Red', url: '/media/product-images/red.png' },
        { name: 'Salmon', url: '/media/product-images/salmon.png' },
        { name: 'Viridian', url: '/media/product-images/viridian.png' },
        { name: 'White', url: '/media/product-images/white.png' }
    ];

    componentDidMount() {
        if (!this.mainRef || !this.secondaryRef || !this.rootRef || !this.dragRef) return;

        const { current: { offsetWidth } } = this.mainRef;
        const { current } = this.rootRef;

        CSS.setVariable(this.secondaryRef, 'secondary-image-width', `${offsetWidth}px`)
        current.addEventListener('wheel', this.rotateImage);
    }

    componentWillUnmount() {
        if (!this.rootRef) return;

        const { current } = this.rootRef;

        current.removeEventListener('wheel', this.rotateImage);
    }

    rotateImage = (event) => {
        if (!this.maskRef || !this.secondaryRef) return;

        event.preventDefault();

        const { deltaY } = event;

        this.currentRotation += 5 * Math.sign(deltaY);

        CSS.setVariable(this.maskRef, 'image-mask-rotate', `${this.currentRotation}deg`)
        CSS.setVariable(this.secondaryRef, 'image-mask-rotate', `${-this.currentRotation}deg`)
    }

    onDragStart = () => {
        if (!this.maskRef) return;

        const { current: { offsetWidth } } = this.maskRef;

        this.originalX = offsetWidth;
    }

    handleMouseDrag = (event) => {
        if (!this.maskRef || !this.mainRef || !this.dragRef) return;

        const { current: { offsetWidth: mainWidth } } = this.mainRef;
        const { current: { offsetWidth: negativeOffset } } = this.dragRef;
        const { translateX } = event;
        const maxWidth = mainWidth - negativeOffset;

        if (this.originalX + (translateX) < 0) {
            CSS.setVariable(this.maskRef, 'image-mask-width', `0px`);
            CSS.setVariable(this.dragRef, 'handle-position', `0px`);

            return;
        } else if (this.originalX + (translateX) > maxWidth) {
            CSS.setVariable(this.maskRef, 'image-mask-width', `${maxWidth}px`);
            CSS.setVariable(this.dragRef, 'handle-position', `${maxWidth}px`);

            return;
        }

        CSS.setVariable(this.maskRef, 'image-mask-width', `${this.originalX + (translateX)}px`);
        CSS.setVariable(this.dragRef, 'handle-position', `${this.originalX + (translateX)}px`);
    };

    render() {
        return (
            <div block="CandleSlicer" ref={ this.rootRef }>
                <Image
                  src={ this.candleData[0].url }
                  ratio="square"
                  mix={ { block: 'CandleSlicer', elem: 'MainImage' } }
                  imageRef={ this.mainRef }
                />
                <div block="CandleSlicer" elem="Secondary" ref={ this.maskRef }>
                    <Image
                      src={ this.candleData[1].url }
                      ration="square"
                      mix={ { block: 'CandleSlicer', elem: 'SecondaryImage' } }
                      imageRef={ this.secondaryRef }
                    />
                </div>
                <Draggable
                  mix={ { block: 'CandleSlicer', elem: 'Handle' } }
                  onDrag={ this.handleMouseDrag }
                  onDragStart={ this.onDragStart }
                  draggableRef={ this.dragRef }
                >
                    <span />
                </Draggable>
            </div>
        );
    }
}
