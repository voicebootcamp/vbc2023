<?php die("Access Denied"); ?>#x#a:2:{s:6:"result";s:1892:".awesomplete [hidden] {
    display: none;
}

.awesomplete .visually-hidden {
    position: absolute;
    clip: rect(0, 0, 0, 0);
}

.awesomplete {
    display: inline-block;
    position: relative;
}

.awesomplete > input {
    display: block;
}

.awesomplete > ul {
    position: absolute;
    left: 0;
    z-index: 1;
    min-width: 100%;
    box-sizing: border-box;
    list-style: none;
    padding: 0;
    margin: 0;
    background: #fff;
}

.awesomplete > ul:empty {
    display: none;
}

.awesomplete > ul {
	border-radius: .3em;
	margin: .2em 0 0;
	background: hsla(0,0%,100%,.9);
	background: linear-gradient(to bottom right, white, hsla(0,0%,100%,.8));
	border: 1px solid rgba(0,0,0,.3);
	box-shadow: .05em .2em .6em rgba(0,0,0,.2);
	text-shadow: none;
}

@supports (transform: scale(0)) {
	.awesomplete > ul {
		transition: .3s cubic-bezier(.4,.2,.5,1.4);
		transform-origin: 1.43em -.43em;
	}
	
	.awesomplete > ul[hidden],
	.awesomplete > ul:empty {
		opacity: 0;
		transform: scale(0);
		display: block;
		transition-timing-function: ease;
	}
}

	/* Pointer */
	.awesomplete > ul:before {
		content: "";
		position: absolute;
		top: -.43em;
		left: 1em;
		width: 0; height: 0;
		padding: .4em;
		background: white;
		border: inherit;
		border-right: 0;
		border-bottom: 0;
		;
		transform: rotate(45deg);
	}

	.awesomplete > ul > li {
		position: relative;
		padding: .2em .5em;
		cursor: pointer;
	}
	
	.awesomplete > ul > li:hover {
		background: hsl(200, 40%, 80%);
		color: black;
	}
	
	.awesomplete > ul > li[aria-selected="true"] {
		background: hsl(205, 40%, 40%);
		color: white;
	}
	
		.awesomplete mark {
			background: hsl(65, 100%, 50%);
		}
		
		.awesomplete li:hover mark {
			background: hsl(68, 100%, 41%);
		}
		
		.awesomplete li[aria-selected="true"] mark {
			background: hsl(86, 100%, 21%);
			color: inherit;
		}
/*# sourceMappingURL=awesomplete.css.map */";s:6:"output";s:0:"";}