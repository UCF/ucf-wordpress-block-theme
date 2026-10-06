/**
 * Front-end enhancements.
 *
 * Built to build/frontend.js and enqueued on every page by includes/enqueue.php. Each module
 * finds its own markup — a `data-ucf-*` attribute or a component class — and does nothing on a
 * page without it. Every one is an enhancement: with this script blocked, every block still
 * renders, every answer is still in the page and every link still goes somewhere.
 */
import './finder';
import './cite';
import './video';
import './expand';
import './spy';
import './external';
